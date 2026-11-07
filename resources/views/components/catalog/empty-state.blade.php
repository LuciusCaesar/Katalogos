@props([
    'message' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<flux:card class="p-8 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ $message ?? $slot }}
    </p>
    @if ($actionUrl && $actionLabel)
        <div class="mt-4">
            <flux:button :href="$actionUrl" variant="primary" icon="plus">
                {{ $actionLabel }}
            </flux:button>
        </div>
    @endif
</flux:card>
