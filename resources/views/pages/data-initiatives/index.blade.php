<x-layouts::app :title="__('Data Initiatives')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Data Initiatives') }}
            </h1>
            <flux:button :href="route('web.data-initiatives.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($dataInitiatives->isEmpty())
            <x-catalog.empty-state
                :message="request()->routeIs('web.my-initiatives')
                    ? __('No data initiatives assigned to you yet.')
                    : __('No data initiatives found. Create one to get started.')"
            />
        @else
            <x-catalog.table :columns="[
                __('Code'),
                __('Label'),
                __('Description'),
                __('Business Assets'),
                __('Business Objectives'),
                __('Data Steward'),
                __('Data Owner'),
                __('Avg Governance Score'),
                __('Avg Data Quality Score'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($dataInitiatives as $initiative)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.data-initiatives.show', $initiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $initiative->code }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->label }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($initiative->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->businessAssets->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->businessObjectives->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->dataSteward()->first()?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $initiative->dataOwner()->first()?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <x-catalog.score :value="$governanceMetrics[$initiative->id]['average'] ?? null" variant="governance" />
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <x-catalog.score :value="$initiative->avg_data_quality_check_score" />
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.data-initiatives.show', $initiative)"
                            :edit-url="route('web.data-initiatives.edit', $initiative)"
                            :delete-url="route('web.data-initiatives.destroy', $initiative)"
                            :delete-message="__('Are you sure you want to delete this data initiative?')"
                            :team-url="route('web.data-initiatives.team', $initiative)"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $dataInitiatives->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
