<x-layouts::app :title="$businessRule->name">
    <x-catalog.show-layout
        :title="$businessRule->name"
        type-label="__('Business Rule')"
        :breadcrumbs="[
            ['label' => __('Business Rules'), 'href' => route('web.business-rules.index')],
            ['label' => $businessRule->name, 'href' => null],
        ]"
        :definition="$businessRule->description"
        :edit-url="route('web.business-rules.edit', $businessRule)"
        :delete-url="route('web.business-rules.destroy', $businessRule)"
        :delete-message="__('Are you sure you want to delete this business rule?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'business-rule', 'entity' => $businessRule])"
        :back-url="route('web.business-rules.index')"
        back-label="__('Back to Business Rules')"
    >
        <x-slot:badges>
            <flux:badge color="blue">{{ $businessRule->dimension->value }}</flux:badge>
        </x-slot:badges>

        <x-slot:metrics>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-catalog.metric-card :label="__('Minimum Score')">
                    <x-catalog.score :value="$businessRule->min_data_quality_check_score" :decimals="2" />
                </x-catalog.metric-card>
                <x-catalog.metric-card :label="__('Average Score')">
                    <x-catalog.score :value="$businessRule->avg_data_quality_check_score" :decimals="2" />
                </x-catalog.metric-card>
                <x-catalog.metric-card :label="__('Maximum Score')">
                    <x-catalog.score :value="$businessRule->max_data_quality_check_score" :decimals="2" />
                </x-catalog.metric-card>
            </div>
        </x-slot:metrics>

        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$businessRule->businessAssets->count()"
            :empty-message="__('No business assets associated with this business rule.')"
            :empty="$businessRule->businessAssets->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Definition'), __('Domain'), __('Data Initiative')]">
                @foreach ($businessRule->businessAssets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-assets.show', $asset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $asset->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->definition ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->domain?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataInitiative?->label ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        {{-- Data Issues --}}
        <x-catalog.section
            :title="__('Data Issues')"
            :count="$businessRule->dataIssues->count()"
            :empty-message="__('No data issues associated with this business rule.')"
            :empty="$businessRule->dataIssues->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description'), __('Created At')]">
                @foreach ($businessRule->dataIssues as $dataIssue)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataIssue->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataIssue->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataIssue->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        {{-- Data Quality Checks --}}
        <x-catalog.section
            :title="__('Data Quality Checks')"
            :count="$businessRule->dataQualityChecks->count()"
            :empty-message="__('No data quality checks associated with this business rule.')"
            :empty="$businessRule->dataQualityChecks->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description'), __('Score'), __('Data Sources'), __('Created At')]">
                @foreach ($businessRule->dataQualityChecks as $dataQualityCheck)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataQualityCheck->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataQualityCheck->description ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <x-catalog.score :value="$dataQualityCheck->latestScore?->score" :decimals="2" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataQualityCheck->dataSources->pluck('name')->implode(', ') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataQualityCheck->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$businessRule" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
