<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessAssetRequest extends FormRequest
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
            'definition' => ['required', 'string'],
            'data_initiative_id' => ['required', 'exists:data_initiatives,id'],
            'domain_id' => ['required', 'exists:domains,id'],
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
            'name.required' => __('The business asset name is required.'),
            'name.string' => __('The name must be a string.'),
            'name.max' => __('The name may not be greater than 255 characters.'),
            'definition.required' => __('The definition is required.'),
            'definition.string' => __('The definition must be a string.'),
            'data_initiative_id.required' => __('The data initiative is required.'),
            'data_initiative_id.exists' => __('The selected data initiative is invalid.'),
            'domain_id.required' => __('The domain is required.'),
            'domain_id.exists' => __('The selected domain is invalid.'),
        ];
    }
}
