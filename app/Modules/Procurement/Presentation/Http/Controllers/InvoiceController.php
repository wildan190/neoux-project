<?php

namespace App\Modules\Procurement\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Procurement\Domain\Models\Invoice;
use App\Modules\Procurement\Domain\Models\InvoiceItem;
use App\Modules\Procurement\Domain\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');

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

        return view('procurement.invoices.index', compact('buyerInvoices', 'vendorInvoices'));
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
            $totalAmount = 0;

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
                    $totalAmount += $subtotal;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'purchase_order_item_id' => $itemData['po_item_id'],
                        'quantity_invoiced' => $itemData['quantity_invoiced'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            $invoice->update(['total_amount' => $totalAmount]);

            // Trigger 3-Way Matching
            $matchingService = new \App\Modules\Procurement\Domain\Services\ThreeWayMatchingService();
            $matchingService->match($invoice);

            DB::commit();

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Invoice submitted successfully! Matching status: ' . ucfirst($invoice->fresh()->status));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit Invoice: ' . $e->getMessage());
        }
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
}
