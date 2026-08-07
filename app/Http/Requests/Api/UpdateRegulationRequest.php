<?php

namespace App\Http\Requests\Api;

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
            'name' => ['sometimes', 'string', Rule::unique('regulations', 'name')->ignore($this->route('regulation'))],
            'description' => ['nullable', 'string'],
            'documentation_url' => ['nullable', 'string', 'url'],
            'business_asset_ids' => ['nullable', 'array'],
            'business_asset_ids.*' => ['exists:business_assets,id'],
        ];
    }
}
