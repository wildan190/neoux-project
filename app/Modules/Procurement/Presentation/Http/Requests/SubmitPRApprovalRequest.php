<?php

namespace App\Modules\Procurement\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPRApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approver_id' => 'required|exists:users,id',
            'head_approver_id' => 'required|exists:users,id',
        ];
    }
}
