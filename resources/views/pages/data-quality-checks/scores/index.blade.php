<x-layouts::app :title="__('Score History for :name', ['name' => $dataQualityCheck->name])">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Score History for :name', ['name' => $dataQualityCheck->name]) }}
            </h1>
            <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" 
               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Quality Check') }}
            </a>
        </div>

        @if ($scores->isEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center text-gray-500 dark:text-gray-400">
                {{ __('No scores recorded yet.') }}
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Score') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Passed') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Failed') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Origin') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Recorded At') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($scores as $score)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium @if($score->score >= 0.9) text-green-600 dark:text-green-400
                                        @elseif($score->score >= 0.7) text-yellow-600 dark:text-yellow-400
                                        @else text-red-600 dark:text-red-400 @endif">
                                    {{ $score->score_percentage }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $score->rows_passed }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $score->rows_failed }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $score->total_rows }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $score->origin_name ?? $score->origin?->name ?? $score->origin_type }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $score->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $score->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $scores->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
