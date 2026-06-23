<x-layouts::app :title="$dataQualityCheck->name">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $dataQualityCheck->name }}
            </h1>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.data-quality-checks.edit', $dataQualityCheck) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.data-quality-checks.destroy', $dataQualityCheck) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this data quality check?') }}')">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-800 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <!-- Name Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Name') }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $dataQualityCheck->name }}</p>
            </div>

            <!-- Description Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Description') }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $dataQualityCheck->description ?? '-' }}</p>
            </div>

            <!-- Business Rule Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Business Rule') }}</h2>
                @if ($dataQualityCheck->businessRule)
                    <a href="{{ route('web.business-rules.show', $dataQualityCheck->businessRule) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        {{ $dataQualityCheck->businessRule->name }}
                    </a>
                @else
                    <p class="text-gray-600 dark:text-gray-400">-</p>
                @endif
            </div>

            <!-- Data Sources Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Data Sources') }}</h2>
                @if ($dataQualityCheck->dataSources->isNotEmpty())
                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1">
                        @foreach ($dataQualityCheck->dataSources as $dataSource)
                            <li>
                                <a href="{{ route('web.data-sources.show', $dataSource) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    {{ $dataSource->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600 dark:text-gray-400">{{ __('No data sources associated with this data quality check.') }}</p>
                @endif
            </div>

            <!-- Created At Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Created At') }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $dataQualityCheck->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <!-- Updated At Section -->
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $dataQualityCheck->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('web.data-quality-checks.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Quality Checks') }}
            </a>
        </div>
    </div>
</x-layouts::app>
