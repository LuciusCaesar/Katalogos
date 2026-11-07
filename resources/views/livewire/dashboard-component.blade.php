<div class="space-y-8 p-6">
        {{-- Search hero --}}
        <form action="{{ route('search') }}" method="GET" role="search" class="w-full max-w-xl">
            <flux:input
                type="search"
                name="q"
                icon="magnifying-glass"
                :placeholder="__('Search the catalog…')"
                aria-label="{{ __('Search') }}"
            />
        </form>

        {{-- My responsibilities --}}
        @if ($myResponsibilities->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('My responsibilities') }}
                </h2>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($myResponsibilities as $row)
                        <a
                            href="{{ route($row['routeName'], $row['entity']) }}"
                            class="block rounded-xl border border-neutral-200 bg-white p-4 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-zinc-800 dark:hover:bg-zinc-800/50"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <flux:badge color="zinc" size="sm">{{ $row['type'] }}</flux:badge>
                                <flux:badge color="blue" size="sm">{{ $row['role'] }}</flux:badge>
                            </div>
                            <p class="mt-2 truncate font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $row['entity']->name ?? $row['entity']->label }}
                            </p>
                            @if ($row['score'] !== null)
                                <div class="mt-1">
                                    <x-catalog.score :value="$row['score']" variant="governance" />
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Needs attention --}}
        @if ($needsAttention->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Needs attention') }}
                </h2>
                <flux:card class="p-0! overflow-hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($needsAttention as $row)
                            <a
                                href="{{ route($row['routeName'], $row['entity']) }}"
                                class="flex items-center justify-between gap-4 px-6 py-4 transition ease-in-out hover:bg-gray-50 dark:hover:bg-zinc-700"
                            >
                                <div class="min-w-0">
                                    <span class="block truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $row['entity']->name ?? $row['entity']->label }}
                                    </span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">
                                        {{ $row['reason'] }}
                                    </span>
                                </div>
                                <flux:badge color="amber" size="sm" class="shrink-0">{{ $row['type'] }}</flux:badge>
                            </a>
                        @endforeach
                    </div>
                </flux:card>
            </section>
        @endif

        {{-- Governance stats --}}
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Governance') }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-catalog.stat-card :label="__('Data Initiatives')" :value="$dataInitiativesCount" :href="route('web.data-initiatives.index')" icon="folder-open" />
                <x-catalog.stat-card :label="__('Business Assets')" :value="$businessAssetsCount" :href="route('web.business-assets.index')" icon="book-open" />
            </div>
        </section>

        {{-- Data Quality stats --}}
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Data Quality') }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-catalog.stat-card :label="__('Business Rules')" :value="$businessRulesCount" :href="route('web.business-rules.index')" icon="document-text" />
                <x-catalog.stat-card :label="__('Data Quality Issues')" :value="$dataIssuesCount" :href="route('web.data-issues.index')" icon="exclamation-triangle" />
                <x-catalog.stat-card :label="__('Root Causes')" :value="$rootCausesCount" :href="route('web.root-causes.index')" icon="puzzle-piece" />
                <x-catalog.stat-card :label="__('Solutions')" :value="$solutionsCount" :href="route('web.solutions.index')" icon="check-circle" />
            </div>
        </section>

        {{-- Recently updated --}}
        @if ($recentlyUpdated->isNotEmpty())
            <section class="space-y-3">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                    {{ __('Recently updated') }}
                </h2>
                <flux:card class="p-0! overflow-hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($recentlyUpdated as $row)
                            <a
                                href="{{ route($row['routeName'], $row['entity']) }}"
                                class="flex items-center justify-between gap-4 px-6 py-4 transition ease-in-out hover:bg-gray-50 dark:hover:bg-zinc-700"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <flux:badge color="zinc" size="sm" class="shrink-0">{{ $row['type'] }}</flux:badge>
                                    <span class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $row['entity']->name ?? $row['entity']->label }}
                                    </span>
                                </div>
                                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $row['entity']->updated_at->format('Y-m-d') }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </flux:card>
            </section>
        @endif
    </div>
