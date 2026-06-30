<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataInitiativeRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', Rule::unique('data_initiatives', 'code')->ignore($this->route('dataInitiative'))],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'business_objective_ids' => ['nullable', 'array'],
            'business_objective_ids.*' => ['integer', 'exists:business_objectives,id'],
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
            'code.required' => __('The code is required.'),
            'code.string' => __('The code must be a string.'),
            'code.max' => __('The code may not be greater than 255 characters.'),
            'code.unique' => __('The code has already been taken.'),
            'label.required' => __('The label is required.'),
            'label.string' => __('The label must be a string.'),
            'label.max' => __('The label may not be greater than 255 characters.'),
            'description.string' => __('The description must be a string.'),
            'business_objective_ids.array' => __('The business objectives must be an array.'),
            'business_objective_ids.*.integer' => __('The business objective ID must be an integer.'),
            'business_objective_ids.*.exists' => __('The selected business objective does not exist.'),
        ];
    }
}
