<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessEventRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:business_events,name'],
            'description' => ['nullable', 'string'],
            'business_asset_ids' => ['nullable', 'array'],
            'business_asset_ids.*' => ['exists:business_assets,id'],
            'business_process_ids' => ['nullable', 'array'],
            'business_process_ids.*' => ['exists:business_processes,id'],
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
            'name.required' => __('The name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'name.unique' => __('The name has already been taken.'),
            'description.string' => __('The description must be a string.'),
            'business_asset_ids.*.exists' => __('One or more selected business assets are invalid.'),
            'business_process_ids.*.exists' => __('One or more selected business processes are invalid.'),
        ];
    }
}
