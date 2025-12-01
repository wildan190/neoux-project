<?php

namespace App\Modules\Company\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'business_category' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:buyer,supplier,vendor'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'npwp' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['required', 'string'],
            'documents' => ['required', 'array'],
            'documents.*' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ];
    }
}
