<?php

namespace Modules\Catalogue\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogueSkuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                // Unique rule needs to be handled carefully in controller or here if possible, scoped to company?
                // For now just basic unique on table
                // Rule::unique('catalogue_items', 'sku')->ignore($this->route('item')),
                // but we will handle update manually or use unique validator
            ],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required_with:attributes.*.value|string',
            'attributes.*.value' => 'required_with:attributes.*.key|string',
            'images.*' => 'image|max:2048',
            'is_active' => 'boolean',
        ];
    }
}
