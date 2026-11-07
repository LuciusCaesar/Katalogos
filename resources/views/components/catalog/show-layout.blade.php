@props([
    'title' => null,
    'typeLabel' => null,
    /** @var array<int, array{label: string, href: string|null}> */
    'breadcrumbs' => [],
    'definition' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMessage' => null,
    'diagramUrl' => null,
    'teamUrl' => null,
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

    {{-- Header: name + badges on the left, actions on the right --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h1>
                @if ($typeLabel)
                    <flux:badge color="zinc">{{ $typeLabel }}</flux:badge>
                @endif
                {{ $badges ?? '' }}
            </div>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $definition ?? '-' }}
            </p>
        </div>
        <x-catalog.actions
            :edit-url="$editUrl"
            :delete-url="$deleteUrl"
            :delete-message="$deleteMessage"
            :diagram-url="$diagramUrl"
            :team-url="$teamUrl"
        />
    </div>

    {{-- Responsibilities (steward, owner, custodian…) --}}
    {{ $responsibilities ?? '' }}

    {{-- Key metrics (scores, counts) --}}
    {{ $metrics ?? '' }}

    {{-- Main content + optional aside --}}
    @if (isset($aside) && $aside->isNotEmpty())
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                {{ $slot }}
            </div>
            <div class="space-y-4 lg:col-span-1">
                {{ $aside }}
            </div>
        </div>
    @else
        <div class="space-y-6">
            {{ $slot }}
        </div>
    @endif

    {{-- Timestamps --}}
    {{ $meta ?? '' }}

    {{-- Back link --}}
    @if ($backUrl && $backLabel)
        <div>
            <flux:button :href="$backUrl" variant="ghost" icon="arrow-left" size="sm">
                {{ $backLabel }}
            </flux:button>
        </div>
    @endif
</div>
