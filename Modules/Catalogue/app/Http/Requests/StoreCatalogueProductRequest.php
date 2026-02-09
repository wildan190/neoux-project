<?php

namespace Modules\Catalogue\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogueProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:catalogue_categories,id',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',

            // SKU / Variant Fields (First Default Variant)
            'sku' => 'required|string|unique:catalogue_items,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
