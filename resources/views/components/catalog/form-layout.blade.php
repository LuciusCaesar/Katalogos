@props([
    'title' => null,
    /** @var array<int, array{label: string, href: string|null}> */
    'breadcrumbs' => [],
    'backUrl' => null,
    'backLabel' => null,
])

<div class="space-y-6">
    {{-- Breadcrumbs --}}
    @if (count($breadcrumbs) > 0)
        <x-catalog.breadcrumbs :items="$breadcrumbs" />
    @endif

    {{-- Flash messages --}}
    <x-catalog.flash />

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h1>
    </div>

    {{-- Form card --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-800">
        {{ $slot }}
    </div>

    {{-- Back link --}}
    @if ($backUrl && $backLabel)
        <div>
            <flux:button :href="$backUrl" variant="ghost" icon="arrow-left" size="sm">
                {{ $backLabel }}
            </flux:button>
        </div>
    @endif
</div>
