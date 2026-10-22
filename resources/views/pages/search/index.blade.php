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
            {{-- Empty query: invite the user to type something --}}
            <flux:card class="p-8 text-center">
                <flux:icon icon="magnifying-glass" variant="outline" class="mx-auto size-10 text-gray-400 dark:text-gray-500" />
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Start typing above to search across business assets, data issues, root causes, solutions, data sources and business processes.') }}
                </p>
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
            <div class="space-y-8">
                @foreach ($groups as $group)
                    <section>
                        <h2 class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __($group['title']) }}
                            <flux:badge color="zinc" size="sm">{{ count($group['items']) === 5 ? __('5+') : count($group['items']) }}</flux:badge>
                        </h2>

                        <flux:card class="p-0! overflow-hidden">
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($group['items'] as $item)
                                    <a
                                        href="{{ route($group['routeName'], $item) }}"
                                        class="flex items-center justify-between gap-4 px-6 py-4 transition ease-in-out hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->name }}
                                        </span>
                                        <flux:icon icon="chevron-right" variant="mini" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
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
