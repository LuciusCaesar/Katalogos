<x-layouts::app :title="__('Governance Score History')">
    <div class="space-y-6">
        <x-catalog.breadcrumbs
            :items="[
                ['label' => __('Business Assets'), 'href' => route('web.business-assets.index')],
                ['label' => $businessAsset->name, 'href' => route('web.business-assets.show', $businessAsset)],
                ['label' => __('Governance Score History'), 'href' => null],
            ]"
        />

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Governance Score History') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $businessAsset->name }}
            </p>
        </div>

        @if ($businessAsset->governanceScores->isNotEmpty())
            {{-- Current Score --}}
            <flux:card class="space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Current Score') }}
                </h2>
                @if ($businessAsset->governanceScore)
                    <div class="flex items-center gap-4">
                        <span class="text-4xl font-bold">
                            <x-catalog.score :value="$businessAsset->governanceScore->score" variant="governance" />
                        </span>
                        <span class="text-lg text-gray-500 dark:text-gray-400">
                            ({{ number_format($businessAsset->governanceScore->achieved_score, 1) }} / {{ number_format($businessAsset->governanceScore->max_possible_score, 1) }} pts)
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Calculated at') }}: {{ $businessAsset->governanceScore->calculated_at->format('M d, Y H:i') }}
                    </p>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No current score available') }}</p>
                @endif
            </flux:card>

            {{-- History Table --}}
            <x-catalog.section :title="__('Score History')" :count="$businessAsset->governanceScores->count()">
                <x-catalog.table :columns="[
                    __('Date'),
                    __('Score'),
                    __('Change'),
                    ['label' => __('Actions'), 'align' => 'right'],
                ]">
                    @foreach ($businessAsset->governanceScores->take(50) as $score)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $score->calculated_at->format('M d, Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <x-catalog.score :value="$score->score" variant="governance" />
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    ({{ number_format($score->achieved_score, 1) }} / {{ number_format($score->max_possible_score, 1) }} pts)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if ($score->changes)
                                    @foreach ($score->changes as $key => $value)
                                        @if (is_array($value))
                                            {{ $key }}: {{ implode(', ', $value) }}
                                        @else
                                            {{ $key }}: {{ $value }}
                                        @endif
                                        @if (! $loop->last), @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <a href="{{ route('web.business-assets.governance-score.show', [$businessAsset, $score]) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('View Details') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </x-catalog.table>
            </x-catalog.section>

            {{-- Actions --}}
            <div>
                <flux:button :href="route('web.business-assets.governance-score.details', $businessAsset)" variant="primary">
                    {{ __('View Current Details') }}
                </flux:button>
            </div>
        @else
            <x-catalog.empty-state :message="__('No governance score history available for this business asset.')" />
        @endif

        {{-- Back link --}}
        <div>
            <flux:button :href="route('web.business-assets.show', $businessAsset)" variant="ghost" icon="arrow-left" size="sm">
                {{ __('Back to Business Asset') }}
            </flux:button>
        </div>
    </div>
</x-layouts::app>
