@props([
    'title' => null,
])

<div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-zinc-800">
    <h3 class="text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
        {{ $title }}
    </h3>
    <div class="mt-2 text-gray-900 dark:text-gray-100">
        {{ $slot }}
    </div>
</div>
