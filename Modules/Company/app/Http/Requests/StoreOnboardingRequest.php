<?php

namespace Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnboardingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:buyer,vendor',
            'business_category' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'npwp' => 'required|string|unique:companies,npwp',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
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
            'npwp.unique' => 'This NPWP is already registered in our system.',
            'category.in' => 'Please select a valid account type.',
            'website.url' => 'Please enter a valid URL for your website.',
        ];
    }
}
