<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSolutionRequest extends FormRequest
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
            'root_cause_ids' => ['nullable', 'array'],
            'root_cause_ids.*' => ['exists:root_causes,id'],
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
            'name.required' => __('The solution name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'description.string' => __('The description must be a string.'),
            'dimension.required' => __('The dimension is required.'),
            'dimension.in' => __('The dimension must be one of: Process, People, Tool.'),
            'root_cause_ids.*.exists' => __('One or more selected root causes are invalid.'),
        ];
    }
}
