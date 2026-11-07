<x-layouts::app :title="__('Business Assets')">
    <div class="space-y-6">
        {{-- Page header --}}
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Assets') }}
            </h1>
            <flux:button :href="route('web.business-assets.create')" variant="primary" icon="plus">
                {{ __('New') }}
            </flux:button>
        </div>

        <x-catalog.flash />

        @if ($businessAssets->isEmpty())
            <x-catalog.empty-state
                :message="request()->routeIs('web.my-assets')
                    ? __('No business assets assigned to you yet.')
                    : __('No business assets found. Create one to get started.')"
            />
        @else
            <x-catalog.table :columns="[
                __('Name'),
                __('Definition'),
                __('Domain'),
                __('Data Initiative'),
                __('Data Steward'),
                __('Data Owner'),
                __('Governance Score'),
                __('Avg DQ Score'),
                ['label' => __('Actions'), 'align' => 'right'],
            ]">
                @foreach ($businessAssets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-assets.show', $asset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $asset->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->definition ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->domain?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataInitiative?->label ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataSteward()->first()?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $asset->dataOwner()->first()?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <x-catalog.score :value="$asset->governanceScore?->score" variant="governance" />
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <x-catalog.score :value="$asset->avg_data_quality_check_score" :decimals="2" />
                        </td>
                        <x-catalog.row-actions
                            :view-url="route('web.business-assets.show', $asset)"
                            :edit-url="route('web.business-assets.edit', $asset)"
                            :delete-url="route('web.business-assets.destroy', $asset)"
                            :delete-message="__('Are you sure you want to delete this business asset?')"
                            :team-url="route('web.business-assets.team', $asset)"
                        />
                    </tr>
                @endforeach
            </x-catalog.table>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $businessAssets->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
