<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'received_at' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_note' => 'nullable|string|max:255',
            'delivery_order_id' => 'nullable|exists:delivery_orders,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.quantity_good' => 'required|integer|min:0',
            'items.*.quantity_rejected' => 'required|integer|min:0',
            'items.*.rejected_reason' => 'nullable|string|max:255',
            'items.*.condition' => 'nullable|string|max:255',
        ];
    }
}
