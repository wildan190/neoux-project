<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_shipped' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
