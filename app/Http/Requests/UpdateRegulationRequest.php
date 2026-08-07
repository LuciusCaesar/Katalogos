<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegulationRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('regulations', 'name')->ignore($this->route('regulation'))],
            'description' => ['nullable', 'string'],
            'documentation_url' => ['nullable', 'string', 'url'],
            'business_asset_ids' => ['nullable', 'array'],
            'business_asset_ids.*' => ['exists:business_assets,id'],
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
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'name.unique' => __('The name has already been taken.'),
            'description.string' => __('The description must be a string.'),
            'documentation_url.url' => __('The documentation URL must be a valid URL.'),
            'business_asset_ids.*.exists' => __('One or more selected business assets are invalid.'),
        ];
    }
}
