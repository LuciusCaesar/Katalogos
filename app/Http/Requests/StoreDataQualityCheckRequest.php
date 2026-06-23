<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDataQualityCheckRequest extends FormRequest
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
            'business_rule_id' => ['required', 'exists:business_rules,id'],
            'data_source_ids' => ['nullable', 'array'],
            'data_source_ids.*' => ['exists:data_sources,id'],
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
            'name.required' => __('The data quality check name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'description.string' => __('The description must be a string.'),
            'business_rule_id.required' => __('The business rule is required.'),
            'business_rule_id.exists' => __('The selected business rule is invalid.'),
            'data_source_ids.*.exists' => __('One or more selected data sources are invalid.'),
        ];
    }
}
