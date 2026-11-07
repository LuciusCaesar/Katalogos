@props([
    /** Score between 0 and 1, null when no score is recorded. */
    'value' => null,
    'decimals' => 1,
    /** "quality" thresholds: >= 0.9 green, >= 0.7 yellow. "governance": >= 0.8 green, >= 0.5 yellow. */
    'variant' => 'quality',
])

@php
    if ($value === null) {
        $color = 'text-gray-400 dark:text-gray-500';
    } else {
        [$greenAt, $yellowAt] = $variant === 'governance' ? [0.8, 0.5] : [0.9, 0.7];
        $color = $value >= $greenAt
            ? 'text-green-600 dark:text-green-400'
            : ($value >= $yellowAt
                ? 'text-yellow-600 dark:text-yellow-400'
                : 'text-red-600 dark:text-red-400');
    }
@endphp

<span class="font-medium {{ $color }}">
    {{ $value === null ? '-' : number_format($value * 100, $decimals).'%' }}
</span>
