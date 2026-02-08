<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGRRRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goods_receipt_item_id' => 'required|exists:goods_receipt_items,id',
            'issue_type' => 'required|in:damaged,rejected,wrong_item',
            'quantity_affected' => 'required|integer|min:1',
            'issue_description' => 'nullable|string|max:1000',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
        ];
    }
}
