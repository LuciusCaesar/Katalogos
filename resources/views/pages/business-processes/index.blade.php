<x-layouts::app :title="__('Business Processes')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Processes') }}
            </h1>
            <flux:button :href="route('web.business-processes.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($businessProcesses->isEmpty())
            <x-catalog.empty-state :message="__('No business processes found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Assets'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($businessProcesses as $businessProcess)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-processes.show', $businessProcess) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessProcess->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($businessProcess->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessProcess->businessAssets->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessProcess->created_at->format('M d, Y') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.business-processes.show', $businessProcess)"
                            :edit-url="route('web.business-processes.edit', $businessProcess)"
                            :delete-url="route('web.business-processes.destroy', $businessProcess)"
                            :delete-message="__('Are you sure you want to delete this business process?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $businessProcesses->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
