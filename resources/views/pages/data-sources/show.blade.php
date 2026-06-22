<x-layouts::app :title="$dataSource->name">
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $dataSource->name }}
                </h1>
                @if ($dataSource->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $dataSource->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.data-sources.edit', $dataSource) }}"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.data-sources.destroy', $dataSource) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this data source?') }}')">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Business Assets Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Business Assets') }} ({{ $dataSource->businessAssets->count() }})
                </h2>
            </div>
            @if ($dataSource->businessAssets->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No business assets associated with this data source.') }}
                </div>
            @else
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Definition') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Domain') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Initiative') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($dataSource->businessAssets as $asset)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="pt-4">
            <a href="{{ route('web.data-sources.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Sources') }}
            </a>
        </div>
    </div>
</x-layouts::app>
