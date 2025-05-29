<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AccessTokenStoreRequest extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'token' => [
                'required',
                'unique:access_tokens,token',
                'max:255',
                'string',
            ],
            'expires_at' => ['required', 'date'],
        ];
    }
}
