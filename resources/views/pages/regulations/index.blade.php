<x-layouts::app :title="__('Regulations')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Regulations') }}
            </h1>
            <flux:button :href="route('web.regulations.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($regulations->isEmpty())
            <x-catalog.empty-state :message="__('No regulations found. Create one to get started.')" />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Description'),
                __('Business Assets'),
                __('Created At'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($regulations as $regulation)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.regulations.show', $regulation) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $regulation->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($regulation->description ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $regulation->businessAssets->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $regulation->created_at->format('M d, Y') }}
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.regulations.show', $regulation)"
                            :edit-url="route('web.regulations.edit', $regulation)"
                            :delete-url="route('web.regulations.destroy', $regulation)"
                            :delete-message="__('Are you sure you want to delete this regulation?')"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $regulations->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
