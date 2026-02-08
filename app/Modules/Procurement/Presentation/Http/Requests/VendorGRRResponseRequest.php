<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorGRRResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject',
            'vendor_notes' => 'nullable|string|max:500',
        ];
    }
}
