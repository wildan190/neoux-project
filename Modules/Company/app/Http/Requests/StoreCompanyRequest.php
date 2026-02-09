<?php

namespace Modules\Company\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'business_category' => 'required|string|max:255',
            'category' => ['required', Rule::in(['buyer', 'supplier', 'vendor'])],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'npwp' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'locations' => 'nullable|array',
            'locations.*' => 'nullable|string|max:255',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ];
    }
}
