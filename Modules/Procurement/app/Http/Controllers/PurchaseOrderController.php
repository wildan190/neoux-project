<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Company\Models\Company;
use Modules\Procurement\Exports\PurchaseOrderTemplateExport;
use Modules\Procurement\Imports\PurchaseOrderHistoryImport;
use Modules\Procurement\Jobs\ProcessPurchaseOrderImport;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    protected $service;

    public function __construct(PurchaseOrderService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        $currentView = request('view', session('procurement_mode', 'buyer'));

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

        $viewPath = 'procurement::' . $currentView . '.po.index';
        return view($viewPath, compact('buyerPOs', 'vendorPOs', 'recentBuyerPOs', 'recentVendorPOs', 'selectedCompanyId', 'currentView'));
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

        $viewPath = $isBuyer ? 'procurement::buyer.po.show' : 'procurement::vendor.po.show';
        return view($viewPath, compact('purchaseOrder', 'isBuyer', 'isVendor'));
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

        try {
            $this->service->confirmPurchaseOrder($purchaseOrder);
            return redirect()->back()->with('success', 'Purchase Order confirmed successfully!');
        } catch (\Exception $e) {
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

        return view('procurement::po.print', compact('purchaseOrder'));
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

        $pdf = Pdf::loadView('procurement::po.pdf', compact('purchaseOrder'));

        return $pdf->download('PO-' . $purchaseOrder->po_number . '.pdf');
    }

    public function generate(PurchaseRequisition $purchaseRequisition)
    {
        try {
            $purchaseOrder = $this->service->generateFromRequisition($purchaseRequisition, Auth::id());
            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Purchase Order generated successfully! Notification has been sent to the vendor.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Vendor acceptance of PO
     */
    public function vendorAccept(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $this->service->updateVendorAcceptance($purchaseOrder, 'issued', $request->notes);
            return back()->with('success', 'You have accepted the Purchase Order. You can now prepare the delivery.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Vendor rejection of PO
     */
    public function vendorReject(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $this->service->updateVendorAcceptance($purchaseOrder, 'rejected_by_vendor', $request->notes);
            return back()->with('success', 'Purchase Order has been rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Buyer pays escrow (deposits funds)
     */
    public function escrowPay(Request $request, PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only buyer can pay escrow
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        if (!$isBuyer) {
            abort(403, 'Only the buyer can pay escrow.');
        }

        // PO must be accepted (issued) by vendor first
        if (!in_array($purchaseOrder->status, ['issued', 'confirmed'])) {
            return back()->with('error', 'PO must be accepted by vendor before escrow payment.');
        }

        if ($purchaseOrder->escrow_status !== 'pending') {
            return back()->with('error', 'Escrow has already been paid or processed.');
        }

        // Setup Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');

        $orderId = 'PO-' . $purchaseOrder->po_number . '-' . time();
        $grossAmount = $purchaseOrder->adjusted_total_amount;

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => $purchaseOrder->po_number,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => 'Payment for PO ' . $purchaseOrder->po_number
                ]
            ],
            'callbacks' => [
                'finish' => route('procurement.midtrans.finish')
            ]
        ];

        try {
            $snapTransaction = \Midtrans\Snap::createTransaction($params);
            $snapToken = $snapTransaction->token;
            $snapUrl = $snapTransaction->redirect_url;

            // Save reference in DB
            $purchaseOrder->update([
                'escrow_reference' => $orderId,
            ]);

            // If AJAX request (from JS fetch), return token for embedded Snap modal
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'snap_token' => $snapToken,
                    'redirect_url' => $snapUrl,
                ]);
            }

            // Fallback: redirect to Snap payment page
            return redirect($snapUrl);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Snap Error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to generate payment link: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment status directly from Midtrans API and update escrow status.
     * Called by the frontend after onSuccess to handle environments where webhooks can't reach localhost.
     */
    public function verifyPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $orderId = $request->input('order_id') ?? $purchaseOrder->escrow_reference;

        if (!$orderId) {
            return response()->json(['error' => 'No order reference found.'], 400);
        }

        try {
            $result = $this->service->verifyPayment($purchaseOrder, $orderId);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['immediate_disbursement']
                    ? 'Pembayaran dikonfirmasi. Barang sudah diterima sebelumnya, dana otomatis dicairkan ke vendor.'
                    : 'Pembayaran dikonfirmasi. Vendor telah dinotifikasi.',
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans verify error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Manual escrow release (if auto-release didn't trigger)
     */
    public function escrowRelease(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Only buyer can release escrow
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        if (!$isBuyer) {
            abort(403, 'Only the buyer can release escrow.');
        }

        if ($purchaseOrder->escrow_status !== 'paid') {
            return back()->with('error', 'Escrow must be in "paid" status to release.');
        }

        try {
            $this->service->releaseEscrow($purchaseOrder);
            return back()->with('success', '3-Way Match sukses! Pencairan dana ke vendor sedang diproses otomatis oleh sistem.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
            $originalName = $file->getClientOriginalName();
            $sanitizedName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
            $fileName = time() . '_' . $sanitizedName;
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

            // Dispatch Synchronously to avoid "File Not Found" in multi-server/isolated staging environments
            ProcessPurchaseOrderImport::dispatchSync($path, Auth::id(), session('selected_company_id'), $request->import_role);

            return redirect()->route('procurement.po.index')
                ->with('success', 'Import completed successfully. POs are now available in the list.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start import: ' . $e->getMessage());
        }
    }
    /**
     * Repeat Order without specific contract (Direct PO duplication)
     */
    public function repeatOrder(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        // Authorization: Only Buyer can repeat
        $isBuyer = ($purchaseOrder->purchaseRequisition?->company_id == $selectedCompanyId) || ($purchaseOrder->company_id == $selectedCompanyId);
        if (!$isBuyer) {
            abort(403, 'Only the buyer can initiate a repeat order.');
        }

        try {
            $newPo = $this->service->createRepeatOrder($purchaseOrder, $selectedCompanyId, Auth::id());

            return redirect()->route('procurement.po.show', $newPo)
                ->with('success', 'Repeat Order PO has been generated and issued based on the previous transaction.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initialize repeat order: ' . $e->getMessage());
        }
    }
}
