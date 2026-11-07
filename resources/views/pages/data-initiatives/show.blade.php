<x-layouts::app :title="$dataInitiative->label">
    <x-catalog.show-layout
        :title="$dataInitiative->label"
        type-label="__('Data Initiative')"
        :breadcrumbs="[
            ['label' => __('Data Initiatives'), 'href' => route('web.data-initiatives.index')],
            ['label' => $dataInitiative->label, 'href' => null],
        ]"
        :definition="$dataInitiative->description"
        :edit-url="route('web.data-initiatives.edit', $dataInitiative)"
        :delete-url="route('web.data-initiatives.destroy', $dataInitiative)"
        :delete-message="__('Are you sure you want to delete this data initiative?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'data-initiative', 'entity' => $dataInitiative])"
        :team-url="route('web.data-initiatives.team', $dataInitiative)"
        :back-url="route('web.data-initiatives.index')"
        back-label="__('Back to Data Initiatives')"
    >
        <x-slot:badges>
            <flux:badge color="blue">{{ $dataInitiative->code }}</flux:badge>
        </x-slot:badges>

        <x-slot:responsibilities>
            <x-catalog.responsibilities
                :title="__('Assigned Roles')"
                :groups="[
                    ['role' => __('Data Steward'), 'users' => $dataInitiative->dataSteward],
                    ['role' => __('Data Owner'), 'users' => $dataInitiative->dataOwner],
                ]"
            />
        </x-slot:responsibilities>

        <x-slot:metrics>
            <div class="space-y-3">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-catalog.metric-card :label="__('Current Average')">
                        <x-catalog.score :value="$governanceMetrics['average']" variant="governance" />
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Total Governance Points')">
                        {{ number_format($governanceMetrics['total_points'], 1) }} / {{ number_format($governanceMetrics['max_possible_points'], 1) }}
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Business Assets')">
                        {{ $governanceMetrics['scored_assets'] }} / {{ $governanceMetrics['total_assets'] }}
                    </x-catalog.metric-card>
                </div>
                @if ($governanceMetrics['scored_assets'] > 0)
                    <div>
                        <a href="{{ route('web.data-initiatives.governance-score.history', $dataInitiative) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('View Governance Score History') }}
                            <flux:icon icon="chevron-right" class="ml-1 size-4" />
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:metrics>

        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$dataInitiative->businessAssets->count()"
            :empty-message="__('No business assets associated with this data initiative.')"
            :empty="$dataInitiative->businessAssets->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Definition'), __('Domain')]">
                @foreach ($dataInitiative->businessAssets as $businessAsset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-gray-900 hover:text-blue-600 dark:text-gray-100 dark:hover:text-blue-400">
                                {{ $businessAsset->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($businessAsset->definition ?? '-', 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $businessAsset->domain?->name ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:aside>
            <x-catalog.detail-card :title="__('Business Objectives')">
                @if ($dataInitiative->businessObjectives->isNotEmpty())
                    <ul class="space-y-2">
                        @foreach ($dataInitiative->businessObjectives as $businessObjective)
                            <li class="text-sm">
                                <a href="{{ route('web.business-objectives.show', $businessObjective) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $businessObjective->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-gray-400 dark:text-gray-500">-</span>
                @endif
            </x-catalog.detail-card>
        </x-slot:aside>

        <x-slot:meta>
            <x-catalog.timestamps :model="$dataInitiative" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
