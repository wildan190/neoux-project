<?php

namespace Modules\Procurement\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Modules\Company\Models\Company;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\User\Models\User;

class PurchaseOrderHistoryImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $userId;
    protected $companyId;
    protected $importRole;

    public function __construct($userId = null, $companyId = null, $importRole = 'buyer')
    {
        $this->userId    = $userId;
        $this->companyId = $companyId;
        $this->importRole = $importRole;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            if ($rows->isEmpty()) return;

            // Log first row keys for debugging
            $keys = array_keys($rows->first()->toArray());
            Log::info('[PO Import History] Detected Column Keys: ' . implode(', ', $keys));

            // Group by Order No (user's primary grouping key)
            // Slugified "Order No" = order_no
            $poKey = $this->findKey($keys, ['order_no', 'po_number', 'order_number']);
            
            if (!$poKey) {
                throw new \Exception("Could not find 'Order No' or 'Order No' column in the Excel file. Detected keys: " . implode(', ', $keys));
            }

            $groupedRows = $rows->groupBy($poKey);

            foreach ($groupedRows as $poNumber => $items) {
                if (empty($poNumber)) continue;

                $firstItem = $items->first();

                // ── Resolve exact columns from user template ──────────────────────────
                $prNumber       = $firstItem[$this->findKey($keys, ['pr_refference_number', 'pr_reference_number', 'pr_no'])] ?? null;
                $vendorName     = $firstItem[$this->findKey($keys, ['vendor', 'supplier'])] ?? null;
                $dept           = $firstItem[$this->findKey($keys, ['department', 'dept'])] ?? null;
                $clerkName      = $firstItem[$this->findKey($keys, ['clerk', 'created_by'])] ?? null;
                $approvedBy     = $firstItem[$this->findKey($keys, ['approved_by', 'approver'])] ?? null;
                $currency       = $firstItem[$this->findKey($keys, ['currency'])] ?? 'IDR';
                $purchaseType   = $firstItem[$this->findKey($keys, ['purchase_type'])] ?? null;
                $month          = $firstItem[$this->findKey($keys, ['month'])] ?? null;

                // ── Lookup Relations ───────────────────────────────────────────────────
                $matchedVendor   = $vendorName ? Company::where('name', 'like', "%{$vendorName}%")->first() : null;
                $matchedPR       = $prNumber   ? PurchaseRequisition::where('pr_number', $prNumber)->first() : null;
                $matchedCreator  = $clerkName  ? User::where('name', 'like', "%{$clerkName}%")->first() : null;
                $matchedApprover = $approvedBy ? User::where('name', 'like', "%{$approvedBy}%")->first() : null;

                $now = \Illuminate\Support\Carbon::now();

                // ── Calculate Total Amount ─────────────────────────────────────────────
                $totalKey = $this->findKey($keys, ['original_currency_total_amount', 'total_amount', 'amount']);
                $totalAmount = $items->sum(function ($row) use ($totalKey) {
                    return $this->sanitize($row[$totalKey] ?? 0);
                });

                // ── Parse Month into Created At ──────────────────────────────────────
                $createdAt = $now;
                if ($month) {
                    try {
                        // Handle numeric month (1-12) or string month (January-December)
                        if (is_numeric($month)) {
                            $createdAt = \Illuminate\Support\Carbon::create(date('Y'), (int)$month, 1, 0, 0, 0);
                        } else {
                            $createdAt = \Illuminate\Support\Carbon::parse("1 {$month} " . date('Y'));
                        }
                    } catch (\Exception $e) {
                        $createdAt = $now;
                    }
                }

                // ── Handle Duplicate PO Number ────────────────────────────────────────
                $originalPoNumber = $poNumber;
                $counter = 1;
                while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
                    $poNumber = $originalPoNumber . '-' . $counter;
                    $counter++;
                }

                // ── Create Purchase Order ──────────────────────────────────────────────
                $data = [
                    'po_number'              => $poNumber,
                    'purchase_type'          => $purchaseType,
                    'dept'                   => $dept,
                    'month'                  => $month,
                    'currency'               => $currency,
                    'purchase_requisition_id' => $matchedPR ? $matchedPR->id : null,
                    'created_by_user_id'     => $matchedCreator  ? $matchedCreator->id  : ($this->userId ?? \Illuminate\Support\Facades\Auth::id()),
                    'approved_by_user_id'    => $matchedApprover ? $matchedApprover->id : null,
                    'total_amount'           => $totalAmount,
                    'status'                 => 'completed',
                    'escrow_status'          => 'released',
                    'confirmed_at'           => $createdAt,
                    'vendor_accepted_at'     => $createdAt,
                    'escrow_paid_at'         => $createdAt,
                    'escrow_released_at'     => $createdAt,
                    'created_at'             => $createdAt,
                    'updated_at'             => $createdAt,
                ];

                if ($this->importRole === 'vendor') {
                    $data['vendor_company_id'] = $this->companyId;
                    $data['company_id']        = null;
                    $data['historical_vendor_name'] = $vendorName;
                } else {
                    $data['company_id']        = $this->companyId;
                    $data['vendor_company_id'] = $matchedVendor ? $matchedVendor->id : null;
                    $data['historical_vendor_name'] = !$matchedVendor ? $vendorName : null;
                }

                $purchaseOrder = PurchaseOrder::create($data);

                // ── Create Purchase Order Items ────────────────────────────────────────
                foreach ($items as $row) {
                    $qty         = $this->sanitize($row[$this->findKey($keys, ['qty', 'quantity'])] ?? 0);
                    $unitPrice   = $this->sanitize($row[$this->findKey($keys, ['orgi_curr_unit_price', 'unit_price'])] ?? 0);
                    $priceOrig   = $this->sanitize($row[$this->findKey($keys, ['unit_price_in_original_currency', 'price_original'])] ?? 0);
                    $amountOrig  = $this->sanitize($row[$this->findKey($keys, ['amount_in_original_currency', 'subtotal'])] ?? 0);
                    $taxAmount   = $this->sanitize($row[$this->findKey($keys, ['tax_amount_in_original_currency', 'tax'])] ?? 0);
                    $totalIncTax = $this->sanitize($row[$this->findKey($keys, ['original_currency_total_amount', 'total_inc_tax'])] ?? 0);

                    PurchaseOrderItem::create([
                        'purchase_order_id'            => $purchaseOrder->id,
                        'purchase_requisition_item_id' => null,
                        'item_name'                    => $row[$this->findKey($keys, ['inventory_name', 'item_name', 'description'])] ?? 'Unnamed Item',
                        'unit'                         => $row[$this->findKey($keys, ['primary_uom', 'unit'])] ?? null,
                        'business_category'            => $row[$this->findKey($keys, ['purchase_category', 'business_category'])] ?? null,
                        'category'                     => $row[$this->findKey($keys, ['category'])] ?? null,
                        'specifications'               => $row[$this->findKey($keys, ['specifications'])] ?? null,
                        'quantity_ordered'             => $qty,
                        'quantity_received'            => $qty,
                        'unit_price'                   => $unitPrice,
                        'tax_amount'                   => $taxAmount,
                        'total_inc_tax'                => $totalIncTax,
                        'price_idr'                    => $totalIncTax, // Default to total since currency is handled at PO level
                        'price_original'               => $priceOrig,
                        'subtotal'                     => $amountOrig,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[PO Import] Critical Failure: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find the actual key in the row based on a list of possibilities.
     */
    private function findKey(array $keys, array $possibilities)
    {
        foreach ($possibilities as $p) {
            if (in_array($p, $keys)) return $p;
        }
        return null;
    }

    /**
     * Sanitize Indonesian number formatting (Dots = Thousands, Commas = Decimal)
     */
    private function sanitize($value): float
    {
        if ($value === null || $value === '' || in_array(trim((string)$value), ['-', '–', '—'])) {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $s = trim((string) $value);
        $s = preg_replace('/[a-zA-Z\s]+/', '', $s); // Strip "IDR", "Rp", spaces
        
        if ($s === '' || in_array($s, ['-', '–', '—'])) return 0.0;

        $dots   = substr_count($s, '.');
        $commas = substr_count($s, ',');

        if ($dots > 1) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } elseif ($dots === 1 && $commas === 0) {
            if (preg_match('/\.\d{3}$/', $s)) {
                $s = str_replace('.', '', $s);
            }
        } elseif ($dots === 0 && $commas === 1) {
            $s = str_replace(',', '.', $s);
        } elseif ($dots === 1 && $commas === 1) {
            if (strrpos($s, ',') > strrpos($s, '.')) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
            }
        }

        $s = preg_replace('/[^0-9.\-]/', '', $s);
        return is_numeric($s) ? (float) $s : 0.0;
    }
}
