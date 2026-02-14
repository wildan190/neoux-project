<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Company\Models\Company;
use Modules\Procurement\Emails\PurchaseOrderSent;
use Modules\Procurement\Exports\PurchaseOrderTemplateExport;
use Modules\Procurement\Imports\PurchaseOrderHistoryImport;
use Modules\Procurement\Jobs\ProcessPurchaseOrderImport;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionOffer;
use Modules\Procurement\Notifications\PurchaseOrderConfirmed;
use Modules\Procurement\Notifications\PurchaseOrderReceived;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        $currentView = request('view', 'buyer');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            } else {
                return redirect()->back()->with('error', 'Please select a company first.');
            }
        }

        // Base Query Builders (Lightweight)
        $buyerQuery = PurchaseOrder::where(function ($q) use ($selectedCompanyId) {
            $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                $q2->where('company_id', $selectedCompanyId);
            })->orWhere('company_id', $selectedCompanyId);
        });

        $vendorQuery = PurchaseOrder::where('vendor_company_id', $selectedCompanyId);

        if ($currentView === 'buyer') {
            // Full Load for Buyer
            $buyerPOs = (clone $buyerQuery)->with(['purchaseRequisition', 'vendorCompany', 'createdBy'])
                ->latest()
                ->paginate(10, ['*'], 'buyer_page');

            $recentBuyerPOs = (clone $buyerQuery)->with(['purchaseRequisition', 'vendorCompany', 'createdBy'])
                ->where('created_at', '>=', now()->subDays(7))
                ->whereIn('status', ['pending_vendor_acceptance', 'issued', 'confirmed'])
                ->latest()
                ->take(4)
                ->get();

            // Count Only for Vendor (Lightweight Paginator for Badge)
            $vendorCount = $vendorQuery->count();
            $vendorPOs = new \Illuminate\Pagination\LengthAwarePaginator([], $vendorCount, 10);
            $recentVendorPOs = collect();

        } elseif ($currentView === 'vendor') {
            // Full Load for Vendor
            $vendorPOs = (clone $vendorQuery)->with(['purchaseRequisition.company', 'vendorCompany', 'createdBy'])
                ->latest()
                ->paginate(10, ['*'], 'vendor_page');

            $recentVendorPOs = (clone $vendorQuery)->with(['purchaseRequisition.company', 'vendorCompany', 'createdBy'])
                ->where('created_at', '>=', now()->subDays(7))
                ->whereIn('status', ['pending_vendor_acceptance', 'issued', 'confirmed'])
                ->latest()
                ->take(4)
                ->get();

            // Count Only for Buyer (Lightweight Paginator for Badge)
            $buyerCount = $buyerQuery->count();
            $buyerPOs = new \Illuminate\Pagination\LengthAwarePaginator([], $buyerCount, 10);
            $recentBuyerPOs = collect();

        } else {
            // Fallback (should not happen usually)
            $buyerPOs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $vendorPOs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $recentBuyerPOs = collect();
            $recentVendorPOs = collect();
        }

        return view('procurement.po.index', compact('buyerPOs', 'vendorPOs', 'recentBuyerPOs', 'recentVendorPOs', 'selectedCompanyId', 'currentView'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to view this Purchase Order.');
        }

        $purchaseOrder->load([
            'items.purchaseRequisitionItem.catalogueItem',
            'items.goodsReceiptItems.goodsReturnRequest',
            'vendorCompany',
            'buyerCompany', // Added: For Ship To fallback
            'purchaseRequisition.company', // Added: For Ship To address
            'createdBy',
            'deliveryOrders', // Added: Step 3 check
            'goodsReceipts.items.goodsReturnRequest',
            'goodsReceipts.receivedBy',
            'invoices',
        ]);

        return view('procurement.po.show', compact('purchaseOrder', 'isBuyer', 'isVendor'));
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only Vendor can confirm
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to confirm this Purchase Order.');
        }

        if ($purchaseOrder->status !== 'issued') {
            return back()->with('error', 'Purchase Order is already ' . $purchaseOrder->status);
        }

        DB::beginTransaction();
        try {
            $purchaseOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            DB::commit();

            // Notify Buyer (the user who created the PO)
            if ($purchaseOrder->createdBy) {
                $purchaseOrder->createdBy->notify(new PurchaseOrderConfirmed($purchaseOrder));
                \Illuminate\Support\Facades\Log::info('PO Confirmation notification sent to: ' . $purchaseOrder->createdBy->email);
            }

            return redirect()->back()->with('success', 'Purchase Order confirmed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to confirm Purchase Order: ' . $e->getMessage());
        }
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to print this Purchase Order.');
        }

        $purchaseOrder->load(['items.purchaseRequisitionItem.catalogueItem', 'vendorCompany', 'createdBy', 'purchaseRequisition.company']);

        return view('procurement.po.print', compact('purchaseOrder'));
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Buyer or Vendor
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to download this Purchase Order.');
        }

        $purchaseOrder->load(['items.purchaseRequisitionItem.catalogueItem', 'vendorCompany', 'createdBy', 'purchaseRequisition.company']);

        $pdf = Pdf::loadView('procurement.po.pdf', compact('purchaseOrder'));

        return $pdf->download('PO-' . $purchaseOrder->po_number . '.pdf');
    }

    public function generate(PurchaseRequisition $purchaseRequisition)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only PR owner can generate PO
        if ($purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to generate PO for this requisition.');
        }

        if (!$purchaseRequisition->winning_offer_id) {
            return back()->with('error', 'No winning offer selected for this requisition.');
        }

        $offer = PurchaseRequisitionOffer::with('items')->findOrFail($purchaseRequisition->winning_offer_id);

        if ($offer->status !== 'accepted') {
            return back()->with('error', 'The selected winner has not been approved by the Purchasing Manager/Head yet.');
        }

        if ($purchaseRequisition->purchaseOrder) {
            return back()->with('error', 'Purchase Order already exists for this requisition.');
        }

        DB::beginTransaction();
        try {
            // Generate PO Number (PO-YYYY-RANDOM)
            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(6));

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'company_id' => $purchaseRequisition->company_id,
                'purchase_requisition_id' => $purchaseRequisition->id,
                'offer_id' => $offer->id,
                'vendor_company_id' => $offer->company_id,
                'created_by_user_id' => Auth::id(),
                'total_amount' => $offer->total_price,
                'status' => 'pending_vendor_acceptance',
            ]);

            // Calculate negotiation ratio if total price differs from sum of items
            $originalTotal = $offer->items()->sum('subtotal');
            $negotiatedTotal = $offer->total_price;
            $ratio = ($originalTotal > 0) ? ($negotiatedTotal / $originalTotal) : 1;

            foreach ($offer->items as $offerItem) {
                // Adjust unit price and subtotal based on negotiation
                $adjustedUnitPrice = $offerItem->unit_price * $ratio;
                $adjustedSubtotal = $offerItem->subtotal * $ratio;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_requisition_item_id' => $offerItem->purchase_requisition_item_id,
                    'quantity_ordered' => $offerItem->quantity_offered,
                    'quantity_received' => 0,
                    'unit_price' => $adjustedUnitPrice,
                    'subtotal' => $adjustedSubtotal,
                ]);
            }

            $purchaseRequisition->update([
                'po_generated_at' => now(),
                'status' => 'ordered', // PO has been generated
            ]);

            DB::commit();

            try {
                // Find contact person to email: The user who created the winning offer
                $vendorUser = $offer->user;
                if ($vendorUser) {
                    // 1. Send Email Notification
                    \Illuminate\Support\Facades\Mail::to($vendorUser->email)
                        ->send(new PurchaseOrderSent($purchaseOrder));

                    // 2. Send System Notification
                    $vendorUser->notify(new PurchaseOrderReceived($purchaseOrder));
                }
            } catch (\Exception $e) {
                // Don't rollback if email fails, just log it
                \Illuminate\Support\Facades\Log::error('Failed to send PO email to vendor: ' . $e->getMessage());
            }

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Purchase Order generated successfully! Notification has been sent to the vendor.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to generate Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Vendor acceptance of PO
     */
    public function vendorAccept(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending_vendor_acceptance') {
            return back()->withErrors(['error' => 'This PO is not pending acceptance.']);
        }

        $purchaseOrder->update([
            'status' => 'issued', // Official issuance after vendor acceptance
            'vendor_accepted_at' => now(),
            'vendor_notes' => $request->notes,
        ]);

        // Notify the Buyer (PO Creator)
        try {
            if ($purchaseOrder->createdBy) {
                // 1. Send Email Notification (if you have a Mailable for confirmed PO, otherwise skip or create one)
                // \Mail::to($purchaseOrder->createdBy->email)->send(new \Modules\Procurement\Emails\PurchaseOrderConfirmed($purchaseOrder));

                // 2. Send System Notification
                $purchaseOrder->createdBy->notify(new PurchaseOrderConfirmed($purchaseOrder));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send PO confirmation notification: ' . $e->getMessage());
        }

        return back()->with('success', 'You have accepted the Purchase Order. You can now prepare the delivery.');
    }

    /**
     * Vendor rejection of PO
     */
    public function vendorReject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending_vendor_acceptance') {
            return back()->withErrors(['error' => 'This PO is not pending acceptance.']);
        }

        $purchaseOrder->update([
            'status' => 'rejected_by_vendor',
            'vendor_rejected_at' => now(),
            'vendor_notes' => $request->notes,
        ]);

        return back()->with('success', 'Purchase Order has been rejected.');
    }

    public function exportTemplate()
    {
        return Excel::download(new PurchaseOrderTemplateExport, 'po_template.xlsx');
    }

    public function importHistory(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'import_role' => 'required|in:buyer,vendor',
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('temp_imports', $fileName, 'local');

            // Parse for preview
            $data = Excel::toArray(new PurchaseOrderHistoryImport, $path, 'local')[0];

            // Limit preview to first 20 rows
            $previewData = collect($data)->slice(0, 20)->map(function ($row) {
                if (isset($row['vendor_name'])) {
                    $exists = Company::where('name', 'like', "%{$row['vendor_name']}%")->exists();
                    $row['vendor_status'] = $exists ? 'Matched' : 'New/Manual';
                }

                return $row;
            });
            $totalRows = count($data);

            return response()->json([
                'success' => true,
                'preview' => $previewData,
                'total_rows' => $totalRows,
                'temp_path' => $path,
                'import_role' => $request->import_role,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse file: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'temp_path' => 'required',
            'import_role' => 'required|in:buyer,vendor',
        ]);

        try {
            $path = $request->temp_path;

            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                return back()->with('error', 'Temporary file expired or not found.');
            }

            // Dispatch Queue Job
            ProcessPurchaseOrderImport::dispatch($path, Auth::id(), session('selected_company_id'), $request->import_role);

            return redirect()->route('procurement.po.index')
                ->with('success', 'Import has been queued. POs will appear in the list once processed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start import: ' . $e->getMessage());
        }
    }
}
