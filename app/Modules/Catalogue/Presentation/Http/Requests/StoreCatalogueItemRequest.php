<?php

namespace App\Modules\Catalogue\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogueItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Get item ID from route (will be null on create, object on update)
        $item = $this->route('item');
        $itemId = $item ? $item->id : null;

        return [
            'category_id' => 'nullable|exists:catalogue_categories,id',
            'sku' => $itemId
                ? 'required|string|max:255|unique:catalogue_items,sku,'.$itemId
                : 'required|string|max:255|unique:catalogue_items,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*.key' => 'required_with:attributes|string|max:255',
            'attributes.*.value' => 'required_with:attributes|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024', // max 1MB
            'primary_image_index' => 'nullable|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'name.required' => 'Product name is required.',
            'images.*.max' => 'Each image must not exceed 1MB.',
            'images.*.image' => 'File must be an image.',
            'attributes.*.key.required_with' => 'Attribute key is required.',
            'attributes.*.value.required_with' => 'Attribute value is required.',
        ];
    }
}
