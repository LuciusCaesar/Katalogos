<?php

namespace App\Enums;

enum BusinessRuleDimension: string
{
    case Availability = 'availability';
    case Consistency = 'consistency';
    case Timeliness = 'timeliness';
    case Validity = 'validity';

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
            'availability' => __('Availability'),
            'consistency' => __('Consistency'),
            'timeliness' => __('Timeliness'),
            'validity' => __('Validity'),
        ];
    }
}
