<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkDOAsShippedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_number' => 'required|string|max:100',
        ];
    }
}
