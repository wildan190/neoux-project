<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\PurchaseRequisition;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOfferDocument;
use App\Modules\Procurement\Domain\Models\PurchaseRequisitionOfferItem;
use App\Notifications\NewOfferReceived;
use App\Notifications\OfferAccepted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    /**
     * Display all offers for a purchase requisition
     * Only users from the PR creator's company can view
     */
    public function index(PurchaseRequisition $purchaseRequisition)
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        // Authorization: only users from the PR creator's company can view offers
        if ($purchaseRequisition->company_id !== $selectedCompanyId) {
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
            'delivery_time' => 'required|string|max:255',
            'warranty' => 'required|string|max:1000',
            'payment_scheme' => 'required|string|max:1000',
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

        if ($existingOffer && !in_array($existingOffer->status, ['pending', 'negotiating'])) {
            return back()->withErrors(['error' => 'You have already submitted an offer that has been processed.']);
        }

        DB::beginTransaction();
        try {
            // Calculate total price
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['quantity_offered'] * $item['unit_price'];
            }

            // Create offer
            if ($existingOffer) {
                $existingOffer->update([
                    'status' => 'pending', // Reset to pending after update so buyer knows it's new
                    'total_price' => $totalPrice,
                    'notes' => $request->notes,
                    'delivery_time' => $request->delivery_time,
                    'warranty' => $request->warranty,
                    'payment_scheme' => $request->payment_scheme,
                ]);
                $offer = $existingOffer;
                // Delete old items and documents to replace them
                $offer->items()->delete();
                // Note: Keep documents or replace them? Usually replace for a fresh bid.
                $offer->documents()->delete();
            } else {
                $offer = PurchaseRequisitionOffer::create([
                    'purchase_requisition_id' => $purchaseRequisition->id,
                    'company_id' => $selectedCompanyId,
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'notes' => $request->notes,
                    'delivery_time' => $request->delivery_time,
                    'warranty' => $request->warranty,
                    'payment_scheme' => $request->payment_scheme,
                    'bidding_status' => 'pending',
                ]);
            }

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

            // Notify PR Creator
            if ($purchaseRequisition->user) {
                $purchaseRequisition->user->notify(new NewOfferReceived($offer));
            }

            DB::commit();

            return redirect()->route('procurement.pr.show-public', $purchaseRequisition)
                ->with('success', 'Your offer has been submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Offer submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'pr_id' => $purchaseRequisition->id,
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
            'user.userDetail',
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
        $selectedCompanyId = session('selected_company_id');

        // Authorization: only users from the PR creator's company
        if ($purchaseRequisition->company_id !== $selectedCompanyId) {
            abort(403, 'Unauthorized to accept offers for this requisition.');
        }

        if (!in_array($offer->status, ['pending', 'negotiating'])) {
            return back()->withErrors(['error' => 'This offer has already been processed.']);
        }

        DB::beginTransaction();
        try {
            // Set offer as the selected winner (pending approval)
            $offer->update(['status' => 'winning']);

            // Update PR tender status
            $purchaseRequisition->update([
                'winning_offer_id' => $offer->id,
                'tender_status' => 'pending_winner_approval',
            ]);

            DB::commit();

            return redirect()->route('procurement.offers.show', $offer)
                ->with('success', 'Winner selected! Waiting for Purchasing Manager/Head approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to select winner.']);
        }
    }

    /**
     * Approve the selected winner (PM/Head only)
     */
    public function approveWinner(PurchaseRequisitionOffer $offer)
    {
        $purchaseRequisition = $offer->purchaseRequisition;

        // Authorization: only Head Approver, Company Owner/Admin, or Admin
        $selectedCompanyId = session('selected_company_id');
        $isCompanyManager = Auth::user()->companies()
            ->where('companies.id', $purchaseRequisition->company_id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();

        if (Auth::id() !== $purchaseRequisition->head_approver_id && !$isCompanyManager && !Auth::user()->is_admin) {
            abort(403, 'Only the Head Approver, Company Owner/Admin, or Purchasing Manager can approve the winner.');
        }

        if ($offer->status !== 'winning') {
            return back()->withErrors(['error' => 'This offer is not in winning status.']);
        }

        DB::beginTransaction();
        try {
            // Accept this offer
            $offer->update(['status' => 'accepted']);

            // Update PR with winning offer and close the tender
            $purchaseRequisition->update([
                'tender_status' => 'awarded',
                'status' => 'awarded',
            ]);

            // Reject all other pending/negotiating offers
            PurchaseRequisitionOffer::where('purchase_requisition_id', $purchaseRequisition->id)
                ->where('id', '!=', $offer->id)
                ->whereIn('status', ['pending', 'negotiating', 'winning'])
                ->update(['status' => 'rejected']);

            // Notify Winning Vendor
            if ($offer->user) {
                $offer->user->notify(new OfferAccepted($offer));
            }

            DB::commit();

            return redirect()->route('procurement.offers.show', $offer)
                ->with('success', 'Winner approved! Tender awarded and participants notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve winner.']);
        }
    }

    /**
     * Reject an offer
     */
    public function reject(PurchaseRequisitionOffer $offer)
    {
        $purchaseRequisition = $offer->purchaseRequisition;
        $selectedCompanyId = session('selected_company_id');

        // Authorization: only users from the PR creator's company
        if ($purchaseRequisition->company_id !== $selectedCompanyId) {
            abort(403, 'Unauthorized to reject offers for this requisition.');
        }

        if ($offer->status !== 'pending' && $offer->status !== 'negotiating') {
            return back()->withErrors(['error' => 'This offer has already been processed.']);
        }

        $offer->update(['status' => 'rejected']);

        return back()->with('success', 'Offer rejected successfully.');
    }



    /**
     * Submit Negotiation Proposal (Buyer Proposes New Terms)
     */
    public function submitNegotiation(Request $request, PurchaseRequisitionOffer $offer)
    {
        $purchaseRequisition = $offer->purchaseRequisition;
        $selectedCompanyId = session('selected_company_id');

        if ($purchaseRequisition->company_id !== $selectedCompanyId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'total_price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'warranty' => 'required|string',
            'payment_scheme' => 'required|string',
            'notes' => 'nullable|string|max:2000',
            'negotiation_message' => 'nullable|string|max:1000',
        ]);

        $offer->update([
            'total_price' => $request->total_price,
            'delivery_time' => $request->delivery_time,
            'warranty' => $request->warranty,
            'payment_scheme' => $request->payment_scheme,
            'notes' => $request->notes,
            'status' => 'negotiating', // Set status to negotiating
            'negotiation_message' => $request->negotiation_message ?? 'Terms updated by Buyer.',
        ]);

        return back()->with('success', 'Negotiation proposal sent to Vendor.');
    }

    /**
     * Vendor accepts the negotiation proposal
     */
    public function vendorAcceptNegotiation(PurchaseRequisitionOffer $offer)
    {
        // Ensure current user is the vendor or company owner
        if ($offer->user_id !== Auth::id()) {
            // Check if user is admin/member of that company OR the owner
            $isMember = $offer->company->members()->where('users.id', Auth::id())->exists();
            $isOwner = $offer->company->user_id === Auth::id();

            if (!$isMember && !$isOwner) {
                abort(403, 'Unauthorized.');
            }
        }

        $offer->update([
            'status' => 'pending', // Reset to pending (ready for award)
            'negotiation_message' => null, // Clear negotiation status/message
        ]);

        return back()->with('success', 'You have accepted the new terms.');
    }

    /**
     * Vendor rejects the negotiation proposal
     */
    public function vendorRejectNegotiation(PurchaseRequisitionOffer $offer)
    {
        // Ensure current user is the vendor or company owner
        if ($offer->user_id !== Auth::id()) {
            $isMember = $offer->company->members()->where('users.id', Auth::id())->exists();
            $isOwner = $offer->company->user_id === Auth::id();

            if (!$isMember && !$isOwner) {
                abort(403, 'Unauthorized.');
            }
        }

        // What status to set? 'rejected' might mean the whole offer is dead.
        // Maybe we keep it as 'negotiating' but add a "Rejected by Vendor" flag?
        // Or simply revert to 'rejected' status for the whole offer?
        // Let's set it to 'rejected' for now as per "Reject Negotiation" usually means deal off.
        // Or we can add a specific status 'negotiation_rejected' if needed.
        // For simplicity: 'rejected'.

        $offer->update([
            'status' => 'rejected',
            'negotiation_message' => 'Vendor rejected the proposed terms.',
        ]);

        return back()->with('success', 'You have rejected the negotiation proposal.');
    }

    /**
     * Show offers submitted by current user
     */
    public function myOffers()
    {
        $selectedCompanyId = session('selected_company_id');

        $offers = PurchaseRequisitionOffer::where('user_id', Auth::id())
            ->where('company_id', $selectedCompanyId) // Fix: Filter by selected company
            ->with(['purchaseRequisition.company', 'purchaseRequisition.user', 'items', 'purchaseOrder']) // Added purchaseOrder
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

        // Pre-fetch win counts for all companies involved to avoid N+1
        $companyIds = $offers->pluck('company_id')->unique();
        $winCounts = PurchaseRequisitionOffer::whereIn('company_id', $companyIds)
            ->where('status', 'accepted')
            ->selectRaw('company_id, count(*) as count')
            ->groupBy('company_id')
            ->pluck('count', 'company_id');

        foreach ($offers as $offer) {
            $score = 0;

            // 1. Price Score (30%) - Lower price = higher score
            if ($maxPrice > 0) {
                $priceScore = (1 - ($offer->total_price / $maxPrice)) * 30;
                $score += $priceScore;
            }

            // 2. Quantity Match Score (20%)
            $totalOfferedQuantity = $offer->items->sum('quantity_offered');
            if ($totalRequestedQuantity > 0) {
                $quantityScore = min(1, $totalOfferedQuantity / $totalRequestedQuantity) * 20;
                $score += $quantityScore;
            }

            // 3. Delivery Time (10%) - Simple presence check for now
            if (!empty($offer->delivery_time)) {
                $score += 10;
            }

            // 4. Warranty (10%) - Simple presence check for now
            if (!empty($offer->warranty)) {
                $score += 10;
            }

            // 5. Payment Scheme (10%) - Simple presence check for now
            if (!empty($offer->payment_scheme)) {
                $score += 10;
            }

            // 6. Win History Score (10%) - Based on past tender wins
            $wins = $winCounts->get($offer->company_id, 0);
            $winScore = min(1, $wins / 10) * 10;
            $score += $winScore;

            // 7. Response Time Score (10%) - How quick to submit
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
