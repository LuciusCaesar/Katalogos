<x-layouts::app :title="__('Governance Score History')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Governance Score History') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $dataInitiative->label }} ({{ $dataInitiative->code }})
                </p>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('web.data-initiatives.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ __('Data Initiatives') }}
            </a>
            <span class="mx-2">/</span>
            <a href="{{ route('web.data-initiatives.show', $dataInitiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ $dataInitiative->label }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-gray-100">{{ __('Governance Score History') }}</span>
        </nav>

        @if($dataInitiative->governanceScoreHistory->isNotEmpty())
            <!-- Current Score Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Current Average Score') }}
                </h2>
                @if($dataInitiative->average_governance_score)
                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold @if($dataInitiative->average_governance_score >= 0.8) text-green-600 dark:text-green-400
                            @elseif($dataInitiative->average_governance_score >= 0.5) text-yellow-600 dark:text-yellow-400
                            @else text-red-600 dark:text-red-400 @endif">
                            {{ number_format($dataInitiative->average_governance_score * 100, 1) }}%
                        </span>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No current score available') }}</p>
                @endif
            </div>

            <!-- History Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Score History') }} ({{ $dataInitiative->governanceScoreHistory->count() }})
                    </h2>
                </div>
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-zinc-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Date') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Score') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Event') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($dataInitiative->governanceScoreHistory->take(50) as $history)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $history->calculated_at?->format('M d, Y H:i') ?? $history->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="@if($history->score >= 0.8) text-green-600 dark:text-green-400
                                        @elseif($history->score >= 0.5) text-yellow-600 dark:text-yellow-400
                                        @else text-red-600 dark:text-red-400 @endif font-medium">
                                        {{ number_format($history->score * 100, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $history->event ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('web.data-initiatives.governance-score.show', [$dataInitiative, $history]) }}"
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                       title="{{ __('View Details') }}">
                                        {{ __('Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('No governance score history available for this data initiative.') }}
                </p>
            </div>
        @endif

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.data-initiatives.show', $dataInitiative) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Initiative') }}
            </a>
        </div>
    </div>
</x-layouts::app>
