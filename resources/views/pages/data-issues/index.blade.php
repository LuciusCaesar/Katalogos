<x-layouts::app :title="__('Data Issues')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Data Issues') }}
            </h1>
            <flux:button :href="route('web.data-issues.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($dataIssues->isEmpty())
            <x-catalog.empty-state :message="__('No data issues found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Assets'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($dataIssues as $dataIssue)
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
                            <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $dataIssue->business_assets_count }} {{ __('Business Asset', ['count' => $dataIssue->business_assets_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $dataIssue->created_at->format('Y-m-d H:i') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.data-issues.show', $dataIssue)"
                            :edit-url="route('web.data-issues.edit', $dataIssue)"
                            :delete-url="route('web.data-issues.destroy', $dataIssue)"
                            :delete-message="__('Are you sure you want to delete this data issue?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $dataIssues->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
