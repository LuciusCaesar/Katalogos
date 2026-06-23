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

            <!-- Current Score Section -->
            @if ($dataQualityCheck->latestScore)
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Current Score') }}</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-3xl font-bold @if($dataQualityCheck->latestScore->score >= 0.9) text-green-600 dark:text-green-400
                                @elseif($dataQualityCheck->latestScore->score >= 0.7) text-yellow-600 dark:text-yellow-400
                                @else text-red-600 dark:text-red-400 @endif">
                            {{ $dataQualityCheck->latestScore->score_percentage }}
                        </span>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p>{{ __('Passed') }}: {{ $dataQualityCheck->latestScore->rows_passed }}</p>
                            <p>{{ __('Failed') }}: {{ $dataQualityCheck->latestScore->rows_failed }}</p>
                            <p>{{ __('Total') }}: {{ $dataQualityCheck->latestScore->total_rows }}</p>
                            <p>{{ __('Recorded') }}: {{ $dataQualityCheck->latestScore->created_at->format('Y-m-d H:i') }}</p>
                            <p>{{ __('Origin') }}: {{ $dataQualityCheck->latestScore->origin_name ?? $dataQualityCheck->latestScore->origin?->name ?? $dataQualityCheck->latestScore->origin_type }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Score History Link -->
            <div class="mb-6">
                <a href="{{ route('web.data-quality-checks.scores.index', $dataQualityCheck) }}" 
                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                    {{ __('View Score History') }} ({{ $dataQualityCheck->scores()->count() }})
                </a>
            </div>

            <!-- Record New Score Form -->
            <div class="bg-gray-50 dark:bg-zinc-700 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Record New Score') }}</h3>
                <form method="POST" action="{{ route('web.data-quality-checks.scores.store', $dataQualityCheck) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="rows_passed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Rows Passed') }}</label>
                            <input type="number" id="rows_passed" name="rows_passed" min="0" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="rows_failed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Rows Failed') }}</label>
                            <input type="number" id="rows_failed" name="rows_failed" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="total_rows" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Total Rows') }}</label>
                            <input type="number" id="total_rows" name="total_rows" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Notes') }}</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="mt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                            {{ __('Record Score') }}
                        </button>
                    </div>
                </form>
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
