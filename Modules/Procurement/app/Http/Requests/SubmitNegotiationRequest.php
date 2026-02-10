<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitNegotiationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:purchase_requisition_offer_items,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity_offered' => 'required|integer|min:1',
            'delivery_time' => 'required|string',
            'warranty' => 'required|string',
            'payment_scheme' => 'required|string',
            'notes' => 'nullable|string|max:2000',
            'negotiation_message' => 'nullable|string|max:1000',
        ];
    }
}
