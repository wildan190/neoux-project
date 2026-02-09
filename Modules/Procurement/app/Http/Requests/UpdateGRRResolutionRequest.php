<?php

namespace Modules\Procurement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGRRResolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resolution_type' => 'required|in:price_adjustment,replacement,return_refund',
        ];
    }
}
