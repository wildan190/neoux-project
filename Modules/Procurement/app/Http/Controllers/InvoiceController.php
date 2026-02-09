<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Procurement\Models\Invoice;
use Modules\Procurement\Models\InvoiceItem;
use Modules\Procurement\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
<<<<<<< HEAD:app/Modules/Procurement/Presentation/Http/Controllers/InvoiceController.php
=======
use Modules\Procurement\Http\Requests\StoreInvoiceRequest;
use Modules\Procurement\Http\Requests\RejectInvoiceRequest;
>>>>>>> 000eb05 (refactoring to modular architect):Modules/Procurement/app/Http/Controllers/InvoiceController.php

class InvoiceController extends Controller
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
            }
        }

        // Get invoices grouped by PO
        // As Buyer: invoices from POs where I'm the buyer
        $buyerInvoices = Invoice::with(['purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition'])
            ->whereHas('purchaseOrder.purchaseRequisition', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            })
            ->latest()
            ->get()
            ->groupBy('purchase_order_id');

        // As Vendor: invoices from POs where I'm the vendor
        $vendorInvoices = Invoice::with(['purchaseOrder.purchaseRequisition.company'])
            ->whereHas('purchaseOrder', function ($q) use ($selectedCompanyId) {
                $q->where('vendor_company_id', $selectedCompanyId);
            })
            ->latest()
            ->get()
            ->groupBy('purchase_order_id');

        return view('procurement.invoices.index', compact('buyerInvoices', 'vendorInvoices', 'currentView'));
    }

    public function create(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        // Only Vendor can create Invoice
        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create Invoice.');
        }

        $purchaseOrder->load('items.purchaseRequisitionItem.catalogueItem');

        return view('procurement.invoices.create', compact('purchaseOrder'));
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');

        if (!$selectedCompanyId) {
            $firstCompany = Auth::user()->companies()->first();
            if ($firstCompany) {
                $selectedCompanyId = $firstCompany->id;
                session(['selected_company_id' => $selectedCompanyId]);
            }
        }

        if ($purchaseOrder->vendor_company_id != $selectedCompanyId) {
            abort(403, 'Unauthorized to create Invoice.');
        }

        $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_invoiced' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotalAmount = 0;

            // Generate Invoice Number (INV-YYYY-RANDOM)
            $invoiceNumber = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'purchase_order_id' => $purchaseOrder->id,
                'vendor_company_id' => $selectedCompanyId,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'total_amount' => 0, // Will update later
                'status' => 'pending',
            ]);

            foreach ($request->items as $itemData) {
                if ($itemData['quantity_invoiced'] > 0) {
                    $subtotal = $itemData['quantity_invoiced'] * $itemData['unit_price'];
                    $subtotalAmount += $subtotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'purchase_order_item_id' => $itemData['po_item_id'],
                        'quantity_invoiced' => $itemData['quantity_invoiced'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            // Apply debit note deductions from PO
            $totalDeduction = $purchaseOrder->total_deduction ?? 0;
            $finalAmount = max(0, $subtotalAmount - $totalDeduction);

            $invoice->update(['total_amount' => $finalAmount]);

            // Trigger 3-Way Matching
            $matchingService = new \Modules\Procurement\Services\ThreeWayMatchingService;
            $matchingService->match($invoice);

            DB::commit();

            $message = 'Invoice submitted successfully! Matching status: ' . ucfirst($invoice->fresh()->status);
            if ($totalDeduction > 0) {
                $message .= ' (Includes price adjustment: -Rp ' . number_format($totalDeduction, 0, ',', '.') . ')';
            }

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to submit Invoice: ' . $e->getMessage());
        }
    }

    /**
     * Vendor Head Approval
     */
    public function vendorApprove(Invoice $invoice)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($invoice->vendor_company_id != $selectedCompanyId) {
            abort(403);
        }

        if ($invoice->status !== 'matched' && $invoice->status !== 'pending') {
            return back()->with('error', 'Invoice cannot be approved at this stage.');
        }

        $invoice->update(['status' => 'vendor_approved']);

        return back()->with('success', 'Invoice approved by Vendor Head. Waiting for Purchasing approval.');
    }

    /**
     * Purchasing Approval
     */
    public function purchasingApprove(Invoice $invoice)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($invoice->purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403);
        }

        if ($invoice->status !== 'vendor_approved') {
            return back()->with('error', 'Invoice must be approved by Vendor Head first.');
        }

        $invoice->update(['status' => 'purchasing_approved']);

        return back()->with('success', 'Invoice approved by Purchasing. Waiting for Finance payment.');
    }

    /**
     * Finance Approval / Payment
     */
    public function financeApprove(Invoice $invoice)
    {
        $selectedCompanyId = session('selected_company_id');
        // In a real app, check for Finance role
        if ($invoice->purchaseOrder->purchaseRequisition->company_id != $selectedCompanyId) {
            abort(403);
        }

        if ($invoice->status !== 'purchasing_approved') {
            return back()->with('error', 'Invoice must be approved by Purchasing first.');
        }

        $invoice->update(['status' => 'paid']);

        return back()->with('success', 'Invoice marked as Paid. Transaction completed.');
    }

    /**
     * Reject Invoice
     */
    public function reject(Request $request, Invoice $invoice)
    {
        $invoice->update([
            'status' => 'rejected',
            'match_status' => array_merge($invoice->match_status ?? [], ['rejection_reason' => $request->reason])
        ]);

        return back()->with('success', 'Invoice has been rejected.');
    }
    public function show(Invoice $invoice)
    {
        $selectedCompanyId = session('selected_company_id');
        $purchaseOrder = $invoice->purchaseOrder;

        // Authorization: Buyer or Vendor
        $isBuyer = $purchaseOrder->purchaseRequisition->company_id == $selectedCompanyId;
        $isVendor = $purchaseOrder->vendor_company_id == $selectedCompanyId;

        if (!$isBuyer && !$isVendor) {
            abort(403, 'Unauthorized to view this Invoice.');
        }

        $invoice->load(['items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem', 'purchaseOrder']);

        return view('procurement.invoices.show', compact('invoice', 'isBuyer', 'isVendor'));
    }

    /**
     * Print invoice view
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem', 'purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition.company']);

        return view('procurement.invoices.print', compact('invoice'));
    }

    /**
     * Download Invoice as PDF
     */
    public function downloadPdf($id)
    {
        $invoice = \Modules\Procurement\Models\Invoice::findOrFail($id);
        $invoice->load(['items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem', 'purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition.company']);

        $pdf = Pdf::loadView('procurement.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Issue Tax Invoice (Faktur Pajak)
     */
    public function issueTaxInvoice(Invoice $invoice)
    {
        if ($invoice->tax_invoice_number) {
            return back()->with('error', 'Tax Invoice already issued for this invoice.');
        }

        // Generate Tax Invoice Number: FP-YYMM-XXXXXXXX
        $year = date('y');
        $month = date('m');
        $sequential = Invoice::whereNotNull('tax_invoice_number')
            ->whereYear('tax_invoice_issued_at', date('Y'))
            ->whereMonth('tax_invoice_issued_at', date('m'))
            ->count() + 1;

        $taxInvoiceNumber = 'FP-' . $year . $month . '-' . str_pad($sequential, 8, '0', STR_PAD_LEFT);

        $invoice->update([
            'tax_invoice_number' => $taxInvoiceNumber,
            'tax_invoice_issued_at' => now(),
        ]);

        return back()->with('success', 'Tax Invoice issued successfully! Number: ' . $taxInvoiceNumber);
    }

    /**
     * Print Tax Invoice
     */
    public function printTaxInvoice(Invoice $invoice)
    {
        if (!$invoice->tax_invoice_number) {
            return back()->with('error', 'Tax Invoice has not been issued yet.');
        }

        $invoice->load(['items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem', 'purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition.company']);

        return view('procurement.invoices.tax-invoice-print', compact('invoice'));
    }

    /**
     * Download Tax Invoice as PDF
     */
    public function downloadTaxInvoicePdf(Invoice $invoice)
    {
        if (!$invoice->tax_invoice_number) {
            return back()->with('error', 'Tax Invoice has not been issued yet.');
        }

        $invoice->load(['items.purchaseOrderItem.purchaseRequisitionItem.catalogueItem', 'purchaseOrder.vendorCompany', 'purchaseOrder.purchaseRequisition.company']);

        $pdf = Pdf::loadView('procurement.invoices.tax-invoice-print', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Tax-Invoice-' . $invoice->tax_invoice_number . '.pdf');
    }
}
