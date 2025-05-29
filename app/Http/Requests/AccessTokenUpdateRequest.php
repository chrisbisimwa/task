<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AccessTokenUpdateRequest extends FormRequest
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
                Rule::unique('access_tokens', 'token')->ignore(
                    $this->accessToken
                ),
                'max:255',
                'string',
            ],
            'expires_at' => ['required', 'date'],
        ];
    }
}
