<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebitNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deduction_percentage' => 'nullable|numeric|min:0|max:100',
            'deduction_amount' => 'required_without:deduction_percentage|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ];
    }
}
