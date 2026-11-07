@props([
    'label' => null,
    'value' => null,
    'href' => null,
    'icon' => null,
])

<a
    href="{{ $href }}"
    class="block rounded-xl border border-neutral-200 bg-white p-6 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-zinc-800 dark:hover:bg-zinc-800/50"
>
    <div class="flex items-center justify-between gap-2">
        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $label }}</p>
        @if ($icon)
            <flux:icon :icon="$icon" variant="outline" class="size-5 shrink-0 text-neutral-400 dark:text-neutral-500" />
        @endif
    </div>
    <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $value }}</p>
</a>
