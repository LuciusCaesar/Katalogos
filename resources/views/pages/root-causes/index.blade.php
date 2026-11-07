<x-layouts::app :title="__('Root Causes')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Root Causes') }}
            </h1>
            <flux:button :href="route('web.root-causes.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($rootCauses->isEmpty())
            <x-catalog.empty-state :message="__('No root causes found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Dimension'),
                __('Data Issues'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($rootCauses as $rootCause)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.root-causes.show', $rootCause) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $rootCause->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->dimension->value }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.root-causes.show', $rootCause) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $rootCause->data_issues_count }} {{ __('Data Issue', ['count' => $rootCause->data_issues_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rootCause->created_at->format('Y-m-d H:i') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.root-causes.show', $rootCause)"
                            :edit-url="route('web.root-causes.edit', $rootCause)"
                            :delete-url="route('web.root-causes.destroy', $rootCause)"
                            :delete-message="__('Are you sure you want to delete this root cause?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $rootCauses->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
