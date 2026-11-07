<x-layouts::app :title="__('Governance Score Details')">
    <div class="space-y-6">
        <x-catalog.breadcrumbs
            :items="[
                ['label' => __('Business Assets'), 'href' => route('web.business-assets.index')],
                ['label' => $businessAsset->name, 'href' => route('web.business-assets.show', $businessAsset)],
                ['label' => __('Governance Score History'), 'href' => route('web.business-assets.governance-score.history', $businessAsset)],
                ['label' => $governanceScore->calculated_at->format('M d, Y H:i'), 'href' => null],
            ]"
        />

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Governance Score Details') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $businessAsset->name }}
            </p>
        </div>

        {{-- Score Summary --}}
        <flux:card class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Score Summary') }}
            </h2>
            <div class="flex items-center gap-4">
                <span class="text-4xl font-bold">
                    <x-catalog.score :value="$governanceScore->score" variant="governance" />
                </span>
                <span class="text-lg text-gray-500 dark:text-gray-400">
                    ({{ number_format($governanceScore->achieved_score, 1) }} / {{ number_format($governanceScore->max_possible_score, 1) }} pts)
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Calculated at') }}: {{ $governanceScore->calculated_at->format('M d, Y H:i') }}
            </p>
            @if ($governanceScore->changes)
                <div>
                    <p class="mb-2 text-sm font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('Changes') }}
                    </p>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @foreach ($governanceScore->changes as $key => $value)
                            <div class="mb-1">
                                <span class="font-medium">{{ $key }}:</span>
                                @if (is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </flux:card>

        {{-- Criteria List --}}
        <x-catalog.section :title="__('Scoring Criteria')">
            <div class="space-y-3 p-6">
                @php
                    $criteria = collect(App\Services\GovernanceCriteria::all())->keyBy('key');
                @endphp
                @foreach ($governanceScore->criteria_results as $key => $met)
                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-zinc-700/50">
                        <div class="flex items-center gap-3">
                            @if ($met)
                                <flux:icon icon="check-circle" variant="outline" class="size-5 text-green-600 dark:text-green-400" />
                            @else
                                <flux:icon icon="x-circle" variant="outline" class="size-5 text-red-600 dark:text-red-400" />
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $criteria[$key]['name'] ?? $key }}
                                </p>
                                @if ($criteria[$key]['description'] ?? null)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $criteria[$key]['description'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm @if($met) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                                {{ $met ? __('Met') : __('Not Met') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                +{{ $governanceScore->criteria_weights[$key] ?? 0 }} pts
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Max Possible Score') }}: {{ $governanceScore->max_possible_score }}
                </p>
            </div>
        </x-catalog.section>

        {{-- Actions --}}
        <div>
            <flux:button :href="route('web.business-assets.governance-score.history', $businessAsset)" variant="primary">
                {{ __('Back to History') }}
            </flux:button>
        </div>

        {{-- Back link --}}
        <div>
            <flux:button :href="route('web.business-assets.show', $businessAsset)" variant="ghost" icon="arrow-left" size="sm">
                {{ __('Back to Business Asset') }}
            </flux:button>
        </div>
    </div>
</x-layouts::app>
