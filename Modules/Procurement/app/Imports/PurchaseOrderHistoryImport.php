<?php

namespace Modules\Procurement\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Company\Models\Company;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\User\Models\User;

class PurchaseOrderHistoryImport implements ToCollection, WithHeadingRow
{
    protected $userId;

    protected $companyId;

    protected $importRole;

    public function __construct($userId = null, $companyId = null, $importRole = 'buyer')
    {
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->importRole = $importRole;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            // Group by Order No (po_number)
            $groupedRows = $rows->groupBy('order_no');

            foreach ($groupedRows as $poNumber => $items) {
                if (empty($poNumber)) {
                    continue;
                }

                $firstItem = $items->first();
                
                // Find or set references
                $status = $firstItem['status'] ?? 'issued';
                $vendorName = $firstItem['vendor'] ?? null;
                $buyerName = $firstItem['purchase_company'] ?? null;
                $prNumber = $firstItem['pr_reference_number'] ?? null;
                $createdByName = $firstItem['created_by'] ?? ($firstItem['created_by_'] ?? null);
                $approvedByName = $firstItem['approved_by'] ?? ($firstItem['approved_by_'] ?? null);

                $matchedVendor = Company::where('name', 'like', "%{$vendorName}%")->first();
                $matchedBuyer = Company::where('name', 'like', "%{$buyerName}%")->first();
                $matchedPR = PurchaseRequisition::where('pr_number', $prNumber)->first();
                
                $matchedCreator = User::where('name', 'like', "%{$createdByName}%")->first();
                $matchedApprover = User::where('name', 'like', "%{$approvedByName}%")->first();

                $now = \Illuminate\Support\Carbon::now();

                $data = [
                    'po_number' => $poNumber,
                    'purchase_type' => $firstItem['purchase_type'] ?? null,
                    'dept' => $firstItem['dept'] ?? null,
                    'month' => $firstItem['month'] ?? null,
                    'currency' => $firstItem['currency'] ?? 'IDR',
                    'purchase_company_no' => $firstItem['purchase_company_no'] ?? null,
                    'purchase_company_email' => $firstItem['purchase_company_email'] ?? null,
                    'purchase_requisition_id' => $matchedPR ? $matchedPR->id : null,
                    'created_by_user_id' => $matchedCreator ? $matchedCreator->id : ($this->userId ?? \Illuminate\Support\Facades\Auth::id()),
                    'approved_by_user_id' => $matchedApprover ? $matchedApprover->id : null,
                    'total_amount' => $items->sum(function($item) {
                        return $this->sanitizeNumeric(
                            $item['total_inc_tax'] ??
                            $item['total_inc_tax_'] ??
                            $item['original_currency_total_amount'] ??
                            $item['amount_in_original_currency'] ?? 0
                        );
                    }),
                    // Historical POs are always fully completed
                    'status' => 'completed',
                    'escrow_status' => 'released',
                    'confirmed_at' => $now,
                    'vendor_accepted_at' => $now,
                    'escrow_paid_at' => $now,
                    'escrow_released_at' => $now,
                ];

                if ($this->importRole === 'vendor') {
                    $data['vendor_company_id'] = $this->companyId;
                    $data['company_id'] = $matchedBuyer ? $matchedBuyer->id : null;
                    $data['historical_vendor_name'] = $buyerName; 
                } else {
                    $data['company_id'] = $matchedBuyer ? $matchedBuyer->id : ($this->companyId ?? null);
                    $data['vendor_company_id'] = $matchedVendor ? $matchedVendor->id : null;
                    $data['historical_vendor_name'] = ! $matchedVendor ? $vendorName : null;
                }

                $purchaseOrder = PurchaseOrder::create($data);

                foreach ($items as $item) {
                    // Resolve quantity — template uses 'quantity', user file uses 'qty'
                    $qty = $this->sanitizeNumeric(
                        $item['qty'] ?? ($item['quantity'] ?? 0)
                    );

                    // Resolve unit price — template: 'priceunit'/'price/unit', user: 'orgi_curr_unit_price'/'unit_price_in_original_currency'
                    $unitPrice = $this->sanitizeNumeric(
                        $item['priceunit'] ??
                        $item['price_unit'] ??
                        $item['orgi_curr_unit_price'] ??
                        $item['unit_price_in_original_currency'] ??
                        $item['unit_price'] ?? 0
                    );

                    // Resolve tax — template: 'tax', user: 'tax_amount_in_original_currency'
                    $tax = $this->sanitizeNumeric(
                        $item['tax'] ??
                        $item['tax_amount_in_original_currency'] ??
                        $item['tax_amount'] ?? 0
                    );

                    // Resolve total inc tax — template: 'total_inc_tax', user: 'original_currency_total_amount'
                    $totalIncTax = $this->sanitizeNumeric(
                        $item['total_inc_tax'] ??
                        $item['total_inc_tax_'] ??
                        $item['original_currency_total_amount'] ??
                        $item['amount_in_original_currency'] ?? 0
                    );

                    // Resolve price IDR — template: 'price_in_indonesia_rupiah'
                    $priceIdr = $this->sanitizeNumeric(
                        $item['price_in_indonesia_rupiah'] ??
                        $item['price_idr'] ?? 0
                    );

                    // Resolve price original — template: 'in_original_currency', user: 'unit_price_in_original_currency'
                    $priceOriginal = $this->sanitizeNumeric(
                        $item['in_original_currency'] ??
                        $item['unit_price_in_original_currency'] ??
                        $item['price_original'] ?? $unitPrice
                    );

                    // Resolve unit/UOM — template: 'unit', user: 'primary_uom'
                    $unit = $item['unit'] ?? ($item['primary_uom'] ?? null);

                    // Resolve item name — template: 'inventory_name'/'description'
                    $itemName = $item['inventory_name'] ??
                        $item['description'] ??
                        $item['item_description'] ??
                        'Unnamed Item';

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'purchase_requisition_item_id' => null,
                        'item_name' => $itemName,
                        'unit' => $unit,
                        'business_category' => $item['business_category'] ?? null,
                        'category' => $item['category'] ?? null,
                        'specifications' => $item['specifications'] ?? null,
                        'quantity_ordered' => $qty,
                        'quantity_received' => $qty, // Historical = fully received
                        'unit_price' => $unitPrice,
                        'tax_amount' => $tax,
                        'total_inc_tax' => $totalIncTax,
                        'price_idr' => $priceIdr,
                        'price_original' => $priceOriginal,
                        'subtotal' => $totalIncTax,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PO Import Collection Error: '.$e->getMessage());
            throw $e;
        }
    }

    private function sanitizeNumeric($value)
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (float)$value;
        
        // Convert to string for regex operations
        $valueStr = (string)$value;
        
        // Remove currency symbols (e.g. IDR, Rp, $, etc.) and spaces
        $valueStr = preg_replace('/[a-zA-Z\s\$]/', '', $valueStr);

        // If it contains both dot and comma, assume comma is decimal separator (Indonesian format e.g. 1.000.000,00)
        if (str_contains($valueStr, '.') && str_contains($valueStr, ',')) {
            $valueStr = str_replace('.', '', $valueStr);
            $valueStr = str_replace(',', '.', $valueStr);
        } elseif (str_contains($valueStr, ',')) {
            // If it only has comma, assume it's decimal
            $valueStr = str_replace(',', '.', $valueStr);
        }

        // Remove any remaining characters except numbers, dots, and minus
        $valueStr = preg_replace('/[^0-9.-]/', '', $valueStr);
        return is_numeric($valueStr) ? (float)$valueStr : 0;
    }
}
