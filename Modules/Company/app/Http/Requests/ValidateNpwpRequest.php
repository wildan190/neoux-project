<?php

namespace Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateNpwpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'npwp' => 'required|string|min:15|max:20',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'npwp.required' => 'NPWP number is required.',
            'npwp.min' => 'NPWP must be at least 15 digits.',
        ];
    }
}
