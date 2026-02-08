<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

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
            'total_price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'warranty' => 'required|string',
            'payment_scheme' => 'required|string',
            'notes' => 'nullable|string|max:2000',
            'negotiation_message' => 'nullable|string|max:1000',
        ];
    }
}
