<x-layouts::app :title="__('Search')">
    <div class="space-y-6">
        {{-- Page title --}}
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            @if ($query !== '')
                {{ __('Search results for “:query”', ['query' => $query]) }}
            @else
                {{ __('Search') }}
            @endif
        </h1>

        {{-- Search form (kept so users can refine the query from here) --}}
        <form method="GET" action="{{ route('search') }}" role="search" class="w-full max-w-xl">
            <flux:input
                type="search"
                name="q"
                value="{{ $query }}"
                icon="magnifying-glass"
                :placeholder="__('Search…')"
                aria-label="{{ __('Search') }}"
            />
        </form>

        @if ($query === '')
            {{-- Empty query: invite the user to type something, or browse by type --}}
            <flux:card class="p-8 text-center">
                <flux:icon icon="magnifying-glass" variant="outline" class="mx-auto size-10 text-gray-400 dark:text-gray-500" />
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Start typing above to search across the whole catalog.') }}
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    @foreach ($browseTypes as $type)
                        <a
                            href="{{ route($type['route']) }}"
                            class="inline-flex items-center rounded-md border border-neutral-200 bg-white px-2 py-1 text-xs font-medium text-neutral-700 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-zinc-800 dark:text-neutral-300 dark:hover:bg-zinc-700"
                        >
                            {{ __($type['label']) }}
                        </a>
                    @endforeach
                </div>
            </flux:card>
        @elseif (count($groups) === 0)
            {{-- Query without matches --}}
            <flux:card class="p-8 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No results found for ":query". Try a different search.', ['query' => $query]) }}
                </p>
            </flux:card>
        @else
            {{-- Grouped results --}}
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ trans_choice(':count result in :types type|:count results in :types types', collect($groups)->sum(fn ($group) => count($group['items'])), ['types' => count($groups)]) }}
            </p>

            <div class="space-y-8">
                @foreach ($groups as $group)
                    <section>
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __($group['title']) }}
                                <flux:badge color="zinc" size="sm">{{ count($group['items']) === 5 ? __('5+') : count($group['items']) }}</flux:badge>
                            </h2>
                            <a
                                href="{{ route($group['indexRoute'], ['q' => $query]) }}"
                                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ __('View all') }}
                            </a>
                        </div>

                        <flux:card class="p-0! overflow-hidden">
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($group['items'] as $item)
                                    <a
                                        href="{{ route($group['routeName'], $item) }}"
                                        class="flex items-center justify-between gap-4 px-6 py-4 transition ease-in-out hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div class="min-w-0">
                                            <span class="block truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $item->name ?? $item->label }}
                                            </span>
                                            @if ($item->definition ?? $item->description)
                                                <span class="block truncate text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item->definition ?? $item->description }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex shrink-0 items-center gap-3">
                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                {{ $item->updated_at->format('Y-m-d') }}
                                            </span>
                                            <flux:icon icon="chevron-right" variant="mini" class="size-4 text-gray-400 dark:text-gray-500" />
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </flux:card>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
