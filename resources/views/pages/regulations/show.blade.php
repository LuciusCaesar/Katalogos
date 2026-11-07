<x-layouts::app :title="$regulation->name">
    <x-catalog.show-layout
        :title="$regulation->name"
        type-label="__('Regulation')"
        :breadcrumbs="[
            ['label' => __('Regulations'), 'href' => route('web.regulations.index')],
            ['label' => $regulation->name, 'href' => null],
        ]"
        :definition="$regulation->description"
        :edit-url="route('web.regulations.edit', $regulation)"
        :delete-url="route('web.regulations.destroy', $regulation)"
        :delete-message="__('Are you sure you want to delete this regulation?')"
        :back-url="route('web.regulations.index')"
        back-label="__('Back to Regulations')"
    >
        <x-catalog.detail-card :title="__('Documentation URL')">
            @if ($regulation->documentation_url)
                <a href="{{ $regulation->documentation_url }}" target="_blank" class="break-all hover:text-blue-600 dark:hover:text-blue-400">
                    {{ $regulation->documentation_url }}
                </a>
            @else
                <span class="text-gray-400 dark:text-gray-500">-</span>
            @endif
        </x-catalog.detail-card>

        {{-- Business Assets --}}
        <x-catalog.section
            :title="__('Business Assets')"
            :count="$regulation->businessAssets->count()"
            :empty-message="__('No business assets associated.')"
            :empty="$regulation->businessAssets->isEmpty()"
        >
            <div class="px-6 py-4">
                <ul class="list-inside list-disc space-y-2 text-sm">
                    @foreach ($regulation->businessAssets as $businessAsset)
                        <li>
                            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $businessAsset->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-catalog.section>

        <x-slot:meta>
            <x-catalog.timestamps :model="$regulation" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
