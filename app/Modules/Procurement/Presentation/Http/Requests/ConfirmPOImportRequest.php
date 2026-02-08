<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPOImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temp_path' => 'required',
            'import_role' => 'required|in:buyer,vendor',
        ];
    }
}
