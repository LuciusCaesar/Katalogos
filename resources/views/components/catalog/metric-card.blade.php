@props([
    'label' => null,
    'hint' => null,
])

<div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
        {{ $slot }}
    </div>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $hint }}</p>
    @endif
</div>
