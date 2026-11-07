<x-layouts::app :title="__('Solutions')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Solutions') }}
            </h1>
            <flux:button :href="route('web.solutions.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($solutions->isEmpty())
            <x-catalog.empty-state :message="__('No solutions found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Dimension'),
                __('Implementation Status'),
                __('Root Causes'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($solutions as $solution)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.solutions.show', $solution) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $solution->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $solution->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $solution->dimension->value }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $solution->implementation_status?->value ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.solutions.show', $solution) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $solution->root_causes_count }} {{ __('Root Cause', ['count' => $solution->root_causes_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $solution->created_at->format('Y-m-d H:i') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.solutions.show', $solution)"
                            :edit-url="route('web.solutions.edit', $solution)"
                            :delete-url="route('web.solutions.destroy', $solution)"
                            :delete-message="__('Are you sure you want to delete this solution?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $solutions->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
