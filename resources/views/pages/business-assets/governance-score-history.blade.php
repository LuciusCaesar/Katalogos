<x-layouts::app :title="__('Governance Score History')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Governance Score History') }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $businessAsset->name }}
                </p>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('web.business-assets.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ __('Business Assets') }}
            </a>
            <span class="mx-2">/</span>
            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ $businessAsset->name }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-gray-100">{{ __('Governance Score History') }}</span>
        </nav>

        @if($businessAsset->governanceScores->isNotEmpty())
            <!-- Current Score Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Current Score') }}
                </h2>
                @if($businessAsset->governanceScore)
                    <div class="flex items-center space-x-4">
                        <span class="text-4xl font-bold @if($businessAsset->governanceScore->score >= 0.8) text-green-600 dark:text-green-400
                            @elseif($businessAsset->governanceScore->score >= 0.5) text-yellow-600 dark:text-yellow-400
                            @else text-red-600 dark:text-red-400 @endif">
                            {{ number_format($businessAsset->governanceScore->score * 100, 1) }}%
                        </span>
                        <span class="text-lg text-gray-500 dark:text-gray-400">
                            ({{ number_format($businessAsset->governanceScore->score, 2) }}/{{ $businessAsset->governanceScore->max_possible_score }})
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Calculated at') }}: {{ $businessAsset->governanceScore->calculated_at->format('M d, Y H:i') }}
                    </p>
                @else
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No current score available') }}</p>
                @endif
            </div>

            <!-- History Table -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Score History') }} ({{ $businessAsset->governanceScores->count() }})
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
                                {{ __('Change') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($businessAsset->governanceScores->take(50) as $score)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $score->calculated_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="@if($score->score >= 0.8) text-green-600 dark:text-green-400
                                        @elseif($score->score >= 0.5) text-yellow-600 dark:text-yellow-400
                                        @else text-red-600 dark:text-red-400 @endif font-medium">
                                        {{ number_format($score->score * 100, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($score->changes)
                                        @foreach($score->changes as $key => $value)
                                            @if(is_array($value))
                                                {{ $key }}: {{ implode(', ', $value) }}
                                            @else
                                                {{ $key }}: {{ $value }}
                                            @endif
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('web.business-assets.governance-score.show', [$businessAsset, $score]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        {{ __('View Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('web.business-assets.governance-score.details', $businessAsset) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                    {{ __('View Current Details') }}
                </a>
                <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                    {{ __('Back to Business Asset') }}
                </a>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('No governance score history available for this business asset.') }}</p>
            </div>
        @endif
    </div>
</x-layouts::app>