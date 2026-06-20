<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRootCauseRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dimension' => ['required', 'string', 'in:Process,People,Tool'],
            'data_issue_ids' => ['nullable', 'array'],
            'data_issue_ids.*' => ['exists:data_issues,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('The root cause name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'description.string' => __('The description must be a string.'),
            'dimension.required' => __('The dimension is required.'),
            'dimension.in' => __('The dimension must be one of: Process, People, Tool.'),
            'data_issue_ids.*.exists' => __('One or more selected data issues are invalid.'),
        ];
    }
}
