<x-layouts::app :title="__('Data Quality Checks')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Data Quality Checks') }}
            </h1>
            <flux:button :href="route('web.data-quality-checks.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($dataQualityChecks->isEmpty())
            <x-catalog.empty-state :message="__('No data quality checks found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Rule'),
                __('Data Sources'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($dataQualityChecks as $dataQualityCheck)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataQualityCheck->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataQualityCheck->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataQualityCheck->businessRule->name ?? '-' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataQualityCheck->data_sources_count }} {{ __('Data Source', ['count' => $dataQualityCheck->data_sources_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataQualityCheck->created_at->format('Y-m-d H:i') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.data-quality-checks.show', $dataQualityCheck)"
                            :edit-url="route('web.data-quality-checks.edit', $dataQualityCheck)"
                            :delete-url="route('web.data-quality-checks.destroy', $dataQualityCheck)"
                            :delete-message="__('Are you sure you want to delete this data quality check?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $dataQualityChecks->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
