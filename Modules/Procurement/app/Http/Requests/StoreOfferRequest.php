<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:2000',
            'delivery_time' => 'required|string|max:255',
            'warranty' => 'required|string|max:1000',
            'payment_scheme' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:purchase_requisition_items,id',
            'items.*.quantity_offered' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'documents' => 'nullable|array|max:5',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ];
    }
}
