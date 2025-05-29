<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'in:pending,in_progress,done'],
            'due_week' => ['required', 'max:255', 'string'],
        ];
    }
}
