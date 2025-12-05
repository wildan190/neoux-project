<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOfferItem;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOfferDocument;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    /**
     * Display all offers for a purchase requisition
     * Only PR creator can view
     */
    public function index(PurchaseRequisition $purchaseRequisition)
    {
        // Authorization: only PR creator
        if ($purchaseRequisition->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to view offers for this requisition.');
        }

        $offers = $purchaseRequisition->offers()
            ->with(['company', 'user.userDetail', 'items.purchaseRequisitionItem.catalogueItem', 'documents'])
            ->ranked()
            ->get();

        return view('procurement.offers.index', compact('purchaseRequisition', 'offers'));
    }

    /**
     * Store a new offer
     */
    public function store(Request $request, PurchaseRequisition $purchaseRequisition)
    {
        // Validation
        $request->validate([
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:purchase_requisition_items,id',
            'items.*.quantity_offered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Get selected company from session
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            } else {
                return back()->withErrors(['error' => 'Please select a company first.']);
            }
        }

        // Authorization checks
        if ($purchaseRequisition->company_id == $selectedCompanyId) {
            return back()->withErrors(['error' => 'You cannot submit an offer for your own company\'s requisition.']);
        }

        if ($purchaseRequisition->tender_status !== 'open') {
            return back()->withErrors(['error' => 'This tender is no longer accepting offers.']);
        }

        // Check if company already submitted an offer
        $existingOffer = PurchaseRequisitionOffer::where('purchase_requisition_id', $purchaseRequisition->id)
            ->where('company_id', $selectedCompanyId)
            ->first();

        if ($existingOffer) {
            return back()->withErrors(['error' => 'Your company has already submitted an offer for this requisition.']);
        }

        DB::beginTransaction();
        try {
            // Calculate total price
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['quantity_offered'] * $item['unit_price'];
            }

            // Create offer
            $offer = PurchaseRequisitionOffer::create([
                'purchase_requisition_id' => $purchaseRequisition->id,
                'company_id' => $selectedCompanyId,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_price' => $totalPrice,
                'notes' => $request->notes,
            ]);

            // Create offer items
            foreach ($request->items as $item) {
                $subtotal = $item['quantity_offered'] * $item['unit_price'];

                PurchaseRequisitionOfferItem::create([
                    'offer_id' => $offer->id,
                    'purchase_requisition_item_id' => $item['item_id'],
                    'quantity_offered' => $item['quantity_offered'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('procurement/offers/' . $offer->id, $fileName, 'public');

                    PurchaseRequisitionOfferDocument::create([
                        'offer_id' => $offer->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            // Calculate ranking for all offers
            $this->calculateRankings($purchaseRequisition);

            DB::commit();

            return redirect()->route('procurement.pr.show-public', $purchaseRequisition)
                ->with('success', 'Your offer has been submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offer submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'pr_id' => $purchaseRequisition->id
            ]);
            return back()->withErrors(['error' => 'Failed to submit offer: ' . $e->getMessage()]);
        }
    }

    /**
     * Show a single offer
     */
    public function show(PurchaseRequisitionOffer $offer)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseRequisition = $offer->purchaseRequisition;

        // Authorization: Only PR owner or Offer submitter can view
        $isPROwner = $purchaseRequisition->company_id == $selectedCompanyId;
        $isOfferSubmitter = $offer->company_id == $selectedCompanyId;

        if (!$isPROwner && !$isOfferSubmitter) {
            abort(403, 'Unauthorized to view this offer.');
        }

        $offer->load([
            'items.purchaseRequisitionItem.catalogueItem',
            'documents',
            'company',
            'user.userDetail'
        ]);

        $purchaseRequisition->load(['items', 'company']);

        // Determine if current user is the PR owner (for back button routing)
        $isOwner = $isPROwner;

        return view('procurement.offers.show', compact('offer', 'purchaseRequisition', 'isOwner'));
    }

    /**
     * Accept an offer (mark as winner)
     */
    public function accept(PurchaseRequisitionOffer $offer)
    {
        $purchaseRequisition = $offer->purchaseRequisition;

        // Authorization: only PR creator
        if ($purchaseRequisition->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to accept offers for this requisition.');
        }

        if ($offer->status !== 'pending') {
            return back()->withErrors(['error' => 'This offer has already been processed.']);
        }

        DB::beginTransaction();
        try {
            // Accept this offer
            $offer->update(['status' => 'accepted']);

            // Update PR with winning offer and close the tender
            $purchaseRequisition->update([
                'winning_offer_id' => $offer->id,
                'tender_status' => 'awarded',
                'status' => 'awarded', // Close the PR - tender awarded
            ]);

            // Reject all other pending offers
            PurchaseRequisitionOffer::where('purchase_requisition_id', $purchaseRequisition->id)
                ->where('id', '!=', $offer->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            DB::commit();

            return redirect()->route('procurement.offers.show', $offer)
                ->with('success', 'Offer accepted successfully! The tender has been awarded. You can now generate a Purchase Order.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to accept offer. Please try again.']);
        }
    }

    /**
     * Reject an offer
     */
    public function reject(PurchaseRequisitionOffer $offer)
    {
        $purchaseRequisition = $offer->purchaseRequisition;

        // Authorization: only PR creator
        if ($purchaseRequisition->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to reject offers for this requisition.');
        }

        if ($offer->status !== 'pending') {
            return back()->withErrors(['error' => 'This offer has already been processed.']);
        }

        $offer->update(['status' => 'rejected']);

        return back()->with('success', 'Offer rejected successfully.');
    }

    /**
     * Show offers submitted by current user
     */
    public function myOffers()
    {
        $offers = PurchaseRequisitionOffer::where('user_id', Auth::id())
            ->with(['purchaseRequisition.company', 'purchaseRequisition.user', 'items'])
            ->latest()
            ->paginate(10);

        return view('procurement.offers.my-offers', compact('offers'));
    }

    /**
     * Calculate and update ranking scores for all offers of a PR
     */
    private function calculateRankings(PurchaseRequisition $purchaseRequisition)
    {
        $offers = $purchaseRequisition->offers()->with('items.purchaseRequisitionItem')->get();

        if ($offers->isEmpty()) {
            return;
        }

        // Get max price for normalization
        $maxPrice = $offers->max('total_price');

        // Get total requested quantity
        $totalRequestedQuantity = $purchaseRequisition->items->sum('quantity');

        foreach ($offers as $offer) {
            $score = 0;

            // 1. Price Score (40%) - Lower price = higher score
            if ($maxPrice > 0) {
                $priceScore = (1 - ($offer->total_price / $maxPrice)) * 40;
                $score += $priceScore;
            }

            // 2. Quantity Match Score (30%)
            $totalOfferedQuantity = $offer->items->sum('quantity_offered');
            if ($totalRequestedQuantity > 0) {
                $quantityScore = min(1, $totalOfferedQuantity / $totalRequestedQuantity) * 30;
                $score += $quantityScore;
            }

            // 3. Company Rating Score (20%) - Future: based on past performance
            // For now, give default score
            $ratingScore = 15; // 75% of 20
            $score += $ratingScore;

            // 4. Response Time Score (10%) - How quick to submit
            $hoursSincePR = $offer->created_at->diffInHours($purchaseRequisition->created_at);
            $timeScore = max(0, (1 - ($hoursSincePR / 168))) * 10; // 168 hours = 1 week
            $score += $timeScore;

            // Update offer with calculated score
            $offer->update(['rank_score' => round($score, 2)]);
        }

        // Mark top offer as recommended (if score >= 70)
        $topOffer = $offers->sortByDesc('rank_score')->first();

        // Reset all recommendations first
        $offers->each(fn($o) => $o->update(['is_recommended' => false]));

        if ($topOffer && $topOffer->rank_score >= 70) {
            // Check if quantity match is at least 80%
            $topOfferQuantity = $topOffer->items->sum('quantity_offered');
            $quantityMatchPercentage = ($topOfferQuantity / $totalRequestedQuantity) * 100;

            if ($quantityMatchPercentage >= 80) {
                $topOffer->update(['is_recommended' => true]);
            }
        }
    }
}
