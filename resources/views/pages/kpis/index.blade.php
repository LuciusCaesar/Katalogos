<x-layouts::app :title="__('KPIs')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('KPIs') }}
            </h1>
            <flux:button :href="route('web.kpis.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($kpis->isEmpty())
            <x-catalog.empty-state :message="__('No KPIs found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Objective'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($kpis as $kpi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.kpis.show', $kpi) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $kpi->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($kpi->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.business-objectives.show', $kpi->businessObjective) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $kpi->businessObjective->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $kpi->created_at->format('M d, Y') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.kpis.show', $kpi)"
                            :edit-url="route('web.kpis.edit', $kpi)"
                            :delete-url="route('web.kpis.destroy', $kpi)"
                            :delete-message="__('Are you sure you want to delete this KPI?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $kpis->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
