<x-layouts::app :title="__('Business Rules')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Rules') }}
            </h1>
            <flux:button :href="route('web.business-rules.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($businessRules->isEmpty())
            <x-catalog.empty-state :message="__('No business rules found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Dimension'),
                __('Business Assets'),
                __('Data Issues'),
                __('Data Quality Checks'),
                __('Avg Score'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($businessRules as $businessRule)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-rules.show', $businessRule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessRule->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessRule->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessRule->dimension->value }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.business-rules.show', $businessRule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessRule->business_assets_count }} {{ __('Business Asset', ['count' => $businessRule->business_assets_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.business-rules.show', $businessRule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessRule->data_issues_count }} {{ __('Data Issue', ['count' => $businessRule->data_issues_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <a href="{{ route('web.business-rules.show', $businessRule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $businessRule->data_quality_checks_count }} {{ __('Data Quality Check', ['count' => $businessRule->data_quality_checks_count]) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <x-catalog.score :value="$businessRule->avg_data_quality_check_score" :decimals="2" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessRule->created_at->format('Y-m-d H:i') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.business-rules.show', $businessRule)"
                            :edit-url="route('web.business-rules.edit', $businessRule)"
                            :delete-url="route('web.business-rules.destroy', $businessRule)"
                            :delete-message="__('Are you sure you want to delete this business rule?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $businessRules->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
