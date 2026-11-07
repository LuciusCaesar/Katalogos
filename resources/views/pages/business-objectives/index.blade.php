<x-layouts::app :title="__('Business Objectives')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Objectives') }}
            </h1>
            <flux:button :href="route('web.business-objectives.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($businessObjectives->isEmpty())
            <x-catalog.empty-state :message="__('No business objectives found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Data Initiatives'),
                __('KPIs'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($businessObjectives as $businessObjective)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-objectives.show', $businessObjective) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessObjective->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($businessObjective->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessObjective->dataInitiatives->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessObjective->kpis->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessObjective->created_at->format('M d, Y') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.business-objectives.show', $businessObjective)"
                            :edit-url="route('web.business-objectives.edit', $businessObjective)"
                            :delete-url="route('web.business-objectives.destroy', $businessObjective)"
                            :delete-message="__('Are you sure you want to delete this business objective?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $businessObjectives->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
