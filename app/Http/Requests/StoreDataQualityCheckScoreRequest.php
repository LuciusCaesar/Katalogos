<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDataQualityCheckScoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rows_passed' => ['nullable', 'integer', 'min:0'],
            'rows_failed' => ['nullable', 'integer', 'min:0'],
            'total_rows' => ['nullable', 'integer', 'min:0'],
            'origin_type' => ['nullable', 'string', 'max:50'],
            'origin_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $hasRowsPassed = $this->filled('rows_passed');
        $hasRowsFailed = $this->filled('rows_failed');
        $hasTotalRows = $this->filled('total_rows');

        $providedCount = (int) $hasRowsPassed + (int) $hasRowsFailed + (int) $hasTotalRows;

        if ($providedCount >= 2) {
            if ($hasRowsPassed && $hasRowsFailed && ! $hasTotalRows) {
                $this->merge(['total_rows' => $this->rows_passed + $this->rows_failed]);
            } elseif ($hasRowsPassed && ! $hasRowsFailed && $hasTotalRows) {
                $this->merge(['rows_failed' => $this->total_rows - $this->rows_passed]);
            } elseif (! $hasRowsPassed && $hasRowsFailed && $hasTotalRows) {
                $this->merge(['rows_passed' => $this->total_rows - $this->rows_failed]);
            }
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasRowsPassed = $this->filled('rows_passed');
            $hasRowsFailed = $this->filled('rows_failed');
            $hasTotalRows = $this->filled('total_rows');

            $providedCount = (int) $hasRowsPassed + (int) $hasRowsFailed + (int) $hasTotalRows;

            if ($providedCount < 2) {
                $validator->errors()->add(
                    'rows',
                    'At least two of rows_passed, rows_failed, or total_rows must be provided.'
                );

                return;
            }

            $rowsPassed = $this->integer('rows_passed');
            $rowsFailed = $this->integer('rows_failed');
            $totalRows = $this->integer('total_rows');

            if ($hasRowsPassed && $hasRowsFailed && $hasTotalRows) {
                if ($rowsPassed + $rowsFailed !== $totalRows) {
                    $validator->errors()->add(
                        'total_rows',
                        'total_rows must equal rows_passed + rows_failed.'
                    );
                }
            }

            if ($hasRowsPassed && $hasTotalRows) {
                if ($rowsPassed > $totalRows) {
                    $validator->errors()->add(
                        'rows_passed',
                        'rows_passed cannot be greater than total_rows.'
                    );
                }
            }

            if ($hasRowsFailed && $hasTotalRows) {
                if ($rowsFailed > $totalRows) {
                    $validator->errors()->add(
                        'rows_failed',
                        'rows_failed cannot be greater than total_rows.'
                    );
                }
            }

            if ($hasRowsPassed && $hasRowsFailed) {
                if ($rowsPassed + $rowsFailed < 0) {
                    $validator->errors()->add(
                        'rows_passed',
                        'The sum of rows_passed and rows_failed must be positive.'
                    );
                }
            }
        });
    }

    /**
     * Get the validated data.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['rows_passed']) && isset($validated['total_rows']) && $validated['total_rows'] > 0) {
            $validated['score'] = $validated['rows_passed'] / $validated['total_rows'];
        } elseif (isset($validated['rows_passed']) && isset($validated['total_rows'])) {
            $validated['score'] = 0;
        }

        if (! isset($validated['origin_type']) && auth()->check()) {
            $validated['origin_type'] = 'user';
            $validated['origin_id'] = auth()->id();
        }

        return $validated;
    }
}
