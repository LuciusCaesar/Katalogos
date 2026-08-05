<?php

namespace App\Enums;

enum SolutionImplementationStatus: string
{
    case IdentifiedForLaterStage = 'Identified for later stage';
    case Study = 'Study';
    case ToBeApproved = 'To be approved';
    case PlannedForImplementation = 'Planned for implementation';
    case Refused = 'Refused';
    case Implemented = 'Implemented';
    case Ongoing = 'Ongoing';

    /**
     * Get all status values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all status options for select dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'Identified for later stage' => __('Identified for later stage'),
            'Study' => __('Study'),
            'To be approved' => __('To be approved'),
            'Planned for implementation' => __('Planned for implementation'),
            'Refused' => __('Refused'),
            'Implemented' => __('Implemented'),
            'Ongoing' => __('Ongoing'),
        ];
    }
}
