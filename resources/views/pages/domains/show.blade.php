<x-layouts::app :title="$domain->name">
    <x-catalog.show-layout
        :title="$domain->name"
        type-label="__('Domain')"
        :breadcrumbs="[
            ['label' => __('Domains'), 'href' => route('web.domains.index')],
            ['label' => $domain->name, 'href' => null],
        ]"
        :definition="$domain->description"
        :edit-url="route('web.domains.edit', $domain)"
        :delete-url="route('web.domains.destroy', $domain)"
        :delete-message="__('Are you sure you want to delete this domain?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'domain', 'entity' => $domain])"
        :team-url="route('web.domains.team', $domain)"
        :back-url="route('web.domains.index')"
        back-label="__('Back to Domains')"
    >
        <x-slot:responsibilities>
            <x-catalog.responsibilities
                :groups="[
                    ['role' => __('Domain Owner'), 'users' => $domain->domainOwner],
                ]"
            />
        </x-slot:responsibilities>

        {{-- Business Terms --}}
        <x-catalog.section
            :title="__('Business Terms')"
            :count="$domain->businessAssets->count()"
            :empty-message="__('No business terms associated with this domain.')"
            :empty="$domain->businessAssets->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Definition'), __('Data Initiative')]">
                @foreach ($domain->businessAssets as $asset)
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
                            {{ $asset->dataInitiative?->label ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$domain" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
