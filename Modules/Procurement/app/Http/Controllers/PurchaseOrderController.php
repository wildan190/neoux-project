<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionOffer;
use Modules\Procurement\Notifications\PurchaseOrderConfirmed;
use Modules\Procurement\Emails\PurchaseOrderSent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Http\Exports\PurchaseOrderTemplateExport;
use Modules\Procurement\Http\Imports\PurchaseOrderHistoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Modules\Company\Models\Company;
use Modules\Procurement\Http\Requests\ImportPOHistoryRequest;
use Modules\Procurement\Http\Requests\ConfirmPOImportRequest;

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

        // Separate POs by role
        // Status filter
        $status = request('status');

        // Buyer POs: where I'm the buyer (can receive goods)
        $buyerPOsQuery = PurchaseOrder::with(['purchaseRequisition', 'vendorCompany', 'createdBy'])
            ->where(function ($q) use ($selectedCompanyId) {
                $q->whereHas('purchaseRequisition', function ($q2) use ($selectedCompanyId) {
                    $q2->where('company_id', $selectedCompanyId);
                })->orWhere('company_id', $selectedCompanyId);
            });

        if ($status) {
            $buyerPOsQuery->where('status', $status);
        }

        $buyerPOs = $buyerPOsQuery->latest()
            ->paginate(10, ['*'], 'buyer_page');

        // Vendor POs: where I'm the vendor (can create invoice)
        $vendorPOsQuery = PurchaseOrder::with(['purchaseRequisition.company', 'buyerCompany', 'vendorCompany', 'createdBy'])
            ->withCount('invoices')
            ->where('vendor_company_id', $selectedCompanyId);

        if ($status) {
            $vendorPOsQuery->where('status', $status);
        }

        $dashboardFilterLabel = null;
        if (request('filter') === 'need_invoice') {
            $vendorPOsQuery->whereDoesntHave('invoices');
            $dashboardFilterLabel = 'Needs Invoicing (Fulfilled orders without invoices)';
        }

        $vendorPOs = $vendorPOsQuery->latest()
            ->paginate(10, ['*'], 'vendor_page');

        return view('procurement.po.index', compact('buyerPOs', 'vendorPOs', 'selectedCompanyId', 'currentView', 'dashboardFilterLabel'));
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
            'createdBy',
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

            // Send Email Notification to Vendor
            try {
                // Find contact person to email: The user who created the winning offer
                $vendorUser = $offer->user;
                if ($vendorUser) {
                    \Illuminate\Support\Facades\Mail::to($vendorUser->email)
                        ->send(new PurchaseOrderSent($purchaseOrder));
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

    public function importHistory(ImportPOHistoryRequest $request)
    {

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
                'import_role' => $request->import_role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse file: ' . $e->getMessage()
            ], 422);
        }
    }

    public function confirmImport(ConfirmPOImportRequest $request)
    {

        try {
            $path = $request->temp_path;

            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                return back()->with('error', 'Temporary file expired or not found.');
            }

            // Dispatch Queue Job
            \App\Jobs\ProcessPurchaseOrderImport::dispatch($path, Auth::id(), session('selected_company_id'), $request->import_role);

            return redirect()->route('procurement.po.index')
                ->with('success', 'Import has been queued. POs will appear in the list once processed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start import: ' . $e->getMessage());
        }
    }
}
