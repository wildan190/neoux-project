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
            $groupedRows = $rows->groupBy('po_number');

            foreach ($groupedRows as $poNumber => $items) {
                if (empty($poNumber)) {
                    continue;
                }

                $firstItem = $items->first();
                $partnerName = $firstItem['vendor_name'] ?? ($firstItem['customer_name'] ?? null);
                $status = $firstItem['status_issuedconfirmedcompleted'] ?? 'issued';

                $matchedPartner = Company::where('name', 'like', "%{$partnerName}%")->first();

                $data = [
                    'po_number' => $poNumber,
                    'created_by_user_id' => $this->userId ?? \Illuminate\Support\Facades\Auth::id(),
                    'total_amount' => $items->sum('total_item_price'),
                    'status' => $status,
                ];

                if ($this->importRole === 'vendor') {
                    // Importing as Vendor: the current company is the Vendor
                    $data['vendor_company_id'] = $this->companyId;
                    $data['historical_vendor_name'] = $partnerName; // In this context, it's the customer name
                } else {
                    // Importing as Buyer (default): the current company is the Buyer
                    $data['company_id'] = $this->companyId;
                    $data['vendor_company_id'] = $matchedPartner ? $matchedPartner->id : null;
                    $data['historical_vendor_name'] = ! $matchedPartner ? $partnerName : null;
                }

                $purchaseOrder = PurchaseOrder::create($data);

                foreach ($items as $item) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'purchase_requisition_item_id' => null,
                        'item_name' => $item['item_name'] ?? ($item['product_name'] ?? null),
                        'quantity_ordered' => $item['quantity'] ?? 0,
                        'quantity_received' => ($status === 'completed') ? ($item['quantity'] ?? 0) : 0,
                        'unit_price' => $item['unit_price'] ?? 0,
                        'subtotal' => $item['total_item_price'] ?? 0,
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
}
