<?php

namespace App\Http\Requests\Api;

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
            'name' => ['required', 'string', 'unique:business_events,name'],
            'description' => ['nullable', 'string'],
            'business_asset_ids' => ['nullable', 'array'],
            'business_asset_ids.*' => ['exists:business_assets,id'],
            'business_process_ids' => ['nullable', 'array'],
            'business_process_ids.*' => ['exists:business_processes,id'],
        ];
    }
}
