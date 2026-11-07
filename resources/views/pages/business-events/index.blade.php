<x-layouts::app :title="__('Business Events')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Events') }}
            </h1>
            <flux:button :href="route('web.business-events.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($businessEvents->isEmpty())
            <x-catalog.empty-state :message="__('No business events found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Assets'),
                __('Business Processes'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($businessEvents as $businessEvent)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-events.show', $businessEvent) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessEvent->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($businessEvent->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessEvent->businessAssets->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessEvent->businessProcesses->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessEvent->created_at->format('M d, Y') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.business-events.show', $businessEvent)"
                            :edit-url="route('web.business-events.edit', $businessEvent)"
                            :delete-url="route('web.business-events.destroy', $businessEvent)"
                            :delete-message="__('Are you sure you want to delete this business event?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $businessEvents->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
