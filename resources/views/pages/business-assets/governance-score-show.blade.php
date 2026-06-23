<x-layouts::app :title="__('Governance Score Details')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Governance Score Details') }}
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
            <a href="{{ route('web.business-assets.governance-score.history', $businessAsset) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                {{ __('Governance Score History') }}
            </a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $governanceScore->calculated_at->format('M d, Y H:i') }}</span>
        </nav>

        <!-- Score Summary Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ __('Score Summary') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-4xl font-bold @if($governanceScore->score >= 0.8) text-green-600 dark:text-green-400
                    @elseif($governanceScore->score >= 0.5) text-yellow-600 dark:text-yellow-400
                    @else text-red-600 dark:text-red-400 @endif">
                    {{ number_format($governanceScore->score * 100, 1) }}%
                </span>
                <span class="text-lg text-gray-500 dark:text-gray-400">
                    ({{ number_format($governanceScore->score, 2) }}/{{ $governanceScore->max_possible_score }})
                </span>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Calculated at') }}: {{ $governanceScore->calculated_at->format('M d, Y H:i') }}
            </p>
            @if($governanceScore->changes)
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        {{ __('Changes') }}
                    </p>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        @foreach($governanceScore->changes as $key => $value)
                            <div class="mb-1">
                                <span class="font-medium">{{ $key }}:</span>
                                @if(is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Criteria List -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Scoring Criteria') }}
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @php
                        $criteria = App\Models\GovernanceCriterion::whereIn('key', array_keys($governanceScore->criteria_results ?? []))
                            ->get()
                            ->keyBy('key');
                    @endphp
                    @foreach($governanceScore->criteria_results as $key => $met)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($met)
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $criteria[$key]?->name ?? $key }}
                                    </p>
                                    @if($criteria[$key]?->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $criteria[$key]->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm @if($met) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                                    {{ $met ? __('Met') : __('Not Met') }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    +{{ $governanceScore->criteria_weights[$key] ?? 0 }} pts
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Max Possible Score') }}: {{ $governanceScore->max_possible_score }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('web.business-assets.governance-score.history', $businessAsset) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                {{ __('Back to History') }}
            </a>
            <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150">
                {{ __('Back to Business Asset') }}
            </a>
        </div>
    </div>
</x-layouts::app>