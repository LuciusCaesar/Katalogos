@props([
    'title' => null,
    'count' => null,
    'emptyMessage' => null,
    'empty' => null,
])

@php
    $isEmpty = $empty ?? $slot->isEmpty();
@endphp

<div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-800">
    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-zinc-700">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $title }}@if ($count !== null) ({{ $count }})@endif
        </h2>
        {{ $header ?? '' }}
    </div>
    @if ($isEmpty)
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            {{ $emptyMessage }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
