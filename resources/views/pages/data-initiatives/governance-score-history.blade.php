<x-layouts::app :title="__('Governance Score History')">
    <div class="space-y-6">
        <x-catalog.breadcrumbs
            :items="[
                ['label' => __('Data Initiatives'), 'href' => route('web.data-initiatives.index')],
                ['label' => $dataInitiative->label, 'href' => route('web.data-initiatives.show', $dataInitiative)],
                ['label' => __('Governance Score History'), 'href' => null],
            ]"
        />

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Governance Score History') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ $dataInitiative->label }} ({{ $dataInitiative->code }})
            </p>
        </div>

        @if ($governanceHistory !== [])
            {{-- Current Metrics --}}
            <flux:card class="space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Current Metrics') }}
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-catalog.metric-card :label="__('Average Score')">
                        <x-catalog.score :value="$governanceMetrics['average']" variant="governance" />
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Total Points')">
                        {{ number_format($governanceMetrics['total_points'], 1) }} / {{ number_format($governanceMetrics['max_possible_points'], 1) }}
                    </x-catalog.metric-card>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Computed from the governance scores of :scored business assets (out of :total).', ['scored' => $governanceMetrics['scored_assets'], 'total' => $governanceMetrics['total_assets']]) }}
                </p>
            </flux:card>

            {{-- History Table --}}
            <x-catalog.section :title="__('Score History')" :count="count($governanceHistory)">
                <x-catalog.table :columns="[
                    __('Date'),
                    __('Average'),
                    __('Total Points'),
                    __('Scored Assets'),
                    __('Event'),
                ]">
                    @foreach ($governanceHistory as $point)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $point['calculated_at']->format('M d, Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <x-catalog.score :value="$point['average']" variant="governance" />
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($point['total_points'], 1) }} / {{ number_format($point['max_possible_points'], 1) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $point['scored_assets'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $point['event'] }}
                            </td>
                        </tr>
                    @endforeach
                </x-catalog.table>
            </x-catalog.section>
        @else
            <x-catalog.empty-state :message="__('No governance score history available for this data initiative.')" />
        @endif

        {{-- Back link --}}
        <div>
            <flux:button :href="route('web.data-initiatives.show', $dataInitiative)" variant="ghost" icon="arrow-left" size="sm">
                {{ __('Back to Data Initiative') }}
            </flux:button>
        </div>
    </div>
</x-layouts::app>
