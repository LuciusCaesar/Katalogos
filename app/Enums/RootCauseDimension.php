<?php

namespace App\Enums;

enum RootCauseDimension: string
{
    case Process = 'Process';
    case People = 'People';
    case Tool = 'Tool';

    /**
     * Get all dimension values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all dimension options for select dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'Process' => __('Process'),
            'People' => __('People'),
            'Tool' => __('Tool'),
        ];
    }
}
