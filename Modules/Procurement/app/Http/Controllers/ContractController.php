<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Procurement\Models\Contract;
use Modules\Procurement\Models\ContractItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;

class ContractController extends Controller
{
    public function index()
    {
        $selectedCompanyId = session('selected_company_id');
        if (!$selectedCompanyId) {
            return redirect()->back()->with('error', 'Please select a company first.');
        }

        $contracts = Contract::with(['vendor', 'createdBy'])
            ->where('company_id', $selectedCompanyId)
            ->latest()
            ->paginate(10);

        return view('procurement::buyer.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($contract->company_id != $selectedCompanyId) {
            abort(403);
        }

        $contract->load(['vendor', 'items.catalogueItem', 'createdBy', 'sourcePo']);

        return view('procurement::buyer.contracts.show', compact('contract'));
    }

    /**
     * Create a contract from a Previous Purchase Order
     */
    public function createFromOrder(PurchaseOrder $purchaseOrder)
    {
        $selectedCompanyId = session('selected_company_id');
        
        // Ensure user owns the PO as buyer
        if ($purchaseOrder->company_id != $selectedCompanyId && $purchaseOrder->purchaseRequisition?->company_id != $selectedCompanyId) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $contractNumber = 'CON-' . strtoupper(Str::random(8));
            
            $contract = Contract::create([
                'company_id' => $selectedCompanyId,
                'vendor_company_id' => $purchaseOrder->vendor_company_id,
                'contract_number' => $contractNumber,
                'title' => 'Annual Contract: ' . ($purchaseOrder->vendorCompany->name ?? 'Vendor'),
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
                'source_po_id' => $purchaseOrder->id,
                'created_by_user_id' => Auth::id(),
                'notes' => 'Contract generated from PO ' . $purchaseOrder->po_number,
            ]);

            foreach ($purchaseOrder->items as $item) {
                ContractItem::create([
                    'contract_id' => $contract->id,
                    'catalogue_item_id' => $item->purchaseRequisitionItem->catalogue_item_id,
                    'fixed_price' => $item->unit_price,
                    'currency' => 'IDR',
                ]);
            }

            DB::commit();

            return redirect()->route('procurement.contracts.show', $contract)
                ->with('success', 'Annual Contract has been created successfully based on negotiated prices.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create contract: ' . $e->getMessage());
        }
    }

    /**
     * Execute a Repeat Order from an existing Contract
     */
    public function repeatOrder(Contract $contract)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($contract->company_id != $selectedCompanyId) {
            abort(403);
        }

        if ($contract->status !== 'active') {
            return back()->with('error', 'Only active contracts can be used for repeat orders.');
        }

        DB::beginTransaction();
        try {
            // Generate PR Number
            $prNumber = 'PR-RO-' . date('Y') . '-' . strtoupper(Str::random(6));

            // Create a DIRECT Purchase Requisition
            $requisition = PurchaseRequisition::create([
                'pr_number' => $prNumber,
                'company_id' => $selectedCompanyId,
                'user_id' => Auth::id(),
                'title' => 'Repeat Order: ' . $contract->title,
                'description' => 'Automated repeat order from Contract ' . $contract->contract_number,
                'status' => 'ordered', // Mark as ordered immediately
                'approval_status' => 'approved', // Auto-approve for direct contract orders
                'tender_status' => 'draft',
                'type' => 'direct',
                'contract_id' => $contract->id, // Tracking
            ]);

            foreach ($contract->items as $item) {
                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $requisition->id,
                    'catalogue_item_id' => $item->catalogue_item_id,
                    'quantity' => 1,
                    'price' => $item->fixed_price,
                ]);
            }

            // IMMEDIATELY GENERATE PO
            $poNumber = 'PO-' . date('Y') . '-' . strtoupper(Str::random(6));
            $totalAmount = $contract->items->sum(function($item) {
                return 1 * $item->fixed_price;
            });

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'company_id' => $selectedCompanyId,
                'purchase_requisition_id' => $requisition->id,
                'vendor_company_id' => $contract->vendor_company_id,
                'created_by_user_id' => Auth::id(),
                'approved_by_user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'issued', // Immediately issued for repeat orders
                'vendor_accepted_at' => now(), // Auto-accept since its part of a master agreement
                'purchase_type' => 'contract',
                'month' => date('F'),
                'currency' => 'IDR',
            ]);

            foreach ($requisition->items as $prItem) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_requisition_item_id' => $prItem->id,
                    'quantity_ordered' => $prItem->quantity,
                    'quantity_received' => 0,
                    'unit_price' => $prItem->price,
                    'subtotal' => $prItem->quantity * $prItem->price,
                    'tax_amount' => 0,
                    'tax_rate' => 0,
                    'total_inc_tax' => $prItem->quantity * $prItem->price,
                    'price_idr' => $prItem->price,
                    'price_original' => $prItem->price,
                ]);
            }

            DB::commit();

            return redirect()->route('procurement.po.show', $purchaseOrder)
                ->with('success', 'Repeat Order PO has been generated and issued based on the master contract.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to initialize repeat order: ' . $e->getMessage());
        }
    }

    public function destroy(Contract $contract)
    {
        $selectedCompanyId = session('selected_company_id');
        if ($contract->company_id != $selectedCompanyId) {
            abort(403);
        }

        $contract->delete();

        return redirect()->route('procurement.contracts.index')->with('success', 'Contract archived.');
    }
}
