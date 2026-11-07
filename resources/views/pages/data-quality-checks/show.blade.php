<x-layouts::app :title="$dataQualityCheck->name">
    <x-catalog.show-layout
        :title="$dataQualityCheck->name"
        type-label="__('Data Quality Check')"
        :breadcrumbs="[
            ['label' => __('Data Quality Checks'), 'href' => route('web.data-quality-checks.index')],
            ['label' => $dataQualityCheck->name, 'href' => null],
        ]"
        :definition="$dataQualityCheck->description"
        :edit-url="route('web.data-quality-checks.edit', $dataQualityCheck)"
        :delete-url="route('web.data-quality-checks.destroy', $dataQualityCheck)"
        :delete-message="__('Are you sure you want to delete this data quality check?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'data-quality-check', 'entity' => $dataQualityCheck])"
        :back-url="route('web.data-quality-checks.index')"
        back-label="__('Back to Data Quality Checks')"
    >
        <x-slot:metrics>
            @if ($dataQualityCheck->latestScore)
                <x-catalog.metric-card
                    :label="__('Current Score')"
                    :hint="__('Passed') . ': ' . $dataQualityCheck->latestScore->rows_passed . ' · ' . __('Failed') . ': ' . $dataQualityCheck->latestScore->rows_failed . ' · ' . __('Total') . ': ' . $dataQualityCheck->latestScore->total_rows"
                >
                    <x-catalog.score :value="$dataQualityCheck->latestScore->score" :decimals="2" />
                </x-catalog.metric-card>
            @endif
        </x-slot:metrics>

        {{-- Business Rule --}}
        <x-catalog.section
            :title="__('Business Rule')"
            :empty-message="'-'"
            :empty="! $dataQualityCheck->businessRule"
        >
            @if ($dataQualityCheck->businessRule)
                <div class="px-6 py-4 text-sm">
                    <a href="{{ route('web.business-rules.show', $dataQualityCheck->businessRule) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ $dataQualityCheck->businessRule->name }}
                    </a>
                </div>
            @endif
        </x-catalog.section>

        {{-- Data Sources --}}
        <x-catalog.section
            :title="__('Data Sources')"
            :count="$dataQualityCheck->dataSources->count()"
            :empty-message="__('No data sources associated with this data quality check.')"
            :empty="$dataQualityCheck->dataSources->isEmpty()"
        >
            <div class="px-6 py-4">
                <ul class="list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    @foreach ($dataQualityCheck->dataSources as $dataSource)
                        <li>
                            <a href="{{ route('web.data-sources.show', $dataSource) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $dataSource->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-catalog.section>

        @if ($dataQualityCheck->latestScore)
            <x-catalog.detail-card :title="__('Latest Score Details')">
                <dl class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex justify-between">
                        <dt>{{ __('Recorded') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $dataQualityCheck->latestScore->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Origin') }}</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $dataQualityCheck->latestScore->origin_name ?? $dataQualityCheck->latestScore->origin?->name ?? $dataQualityCheck->latestScore->origin_type }}</dd>
                    </div>
                </dl>
            </x-catalog.detail-card>
        @endif

        <div>
            <a href="{{ route('web.data-quality-checks.scores.index', $dataQualityCheck) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                {{ __('View Score History') }} ({{ $dataQualityCheck->scores()->count() }})
            </a>
        </div>

        {{-- Record New Score --}}
        <flux:card class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Record New Score') }}
            </h2>
            <form method="POST" action="{{ route('web.data-quality-checks.scores.store', $dataQualityCheck) }}">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <flux:input type="number" name="rows_passed" :label="__('Rows Passed')" min="0" />
                    <flux:input type="number" name="rows_failed" :label="__('Rows Failed')" min="0" />
                    <flux:input type="number" name="total_rows" :label="__('Total Rows')" min="0" />
                </div>
                <div class="mt-4">
                    <flux:textarea name="notes" :label="__('Notes')" rows="2" />
                </div>
                <div class="mt-4">
                    <flux:button type="submit" variant="primary">
                        {{ __('Record Score') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>

        <x-slot:meta>
            <x-catalog.timestamps :model="$dataQualityCheck" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
