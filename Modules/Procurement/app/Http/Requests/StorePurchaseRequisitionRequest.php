<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.catalogue_item_id' => 'required|exists:catalogue_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'documents.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Request title is required.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item must be added.',
            'items.*.catalogue_item_id.required' => 'Please select an item.',
            'items.*.catalogue_item_id.exists' => 'Selected item does not exist.',
            'items.*.quantity.required' => 'Quantity is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Price is required.',
            'items.*.price.min' => 'Price cannot be negative.',
        ];
    }
}
