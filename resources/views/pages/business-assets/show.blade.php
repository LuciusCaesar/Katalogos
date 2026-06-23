<x-layouts::app :title="$businessAsset->name">
    <div class="space-y-6">
        <!-- Header Section with Action Buttons -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $businessAsset->name }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $businessAsset->definition ?? '-' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.business-assets.edit', $businessAsset) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-800 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.business-assets.destroy', $businessAsset) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this business asset?') }}')">
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
        
        <!-- Data Quality Check Scores Summary -->
        @if ($businessAsset->businessRules->isNotEmpty())
            @php
                $hasScores = $businessAsset->businessRules
                    ->flatMap(fn ($rule) => $rule->dataQualityChecks)
                    ->filter(fn ($dqc) => $dqc->latestScore !== null)
                    ->isNotEmpty();
            @endphp
            @if ($hasScores)
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Data Quality Check Scores Summary') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Minimum Score') }}</p>
                            <p class="text-2xl font-bold @if($businessAsset->min_data_quality_check_score >= 0.9) text-green-600 dark:text-green-400
                                    @elseif($businessAsset->min_data_quality_check_score >= 0.7) text-yellow-600 dark:text-yellow-400
                                    @else text-red-600 dark:text-red-400 @endif">
                                {{ $businessAsset->min_data_quality_check_score !== null ? number_format($businessAsset->min_data_quality_check_score * 100, 2) . '%' : '-' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Average Score') }}</p>
                            <p class="text-2xl font-bold @if($businessAsset->avg_data_quality_check_score >= 0.9) text-green-600 dark:text-green-400
                                    @elseif($businessAsset->avg_data_quality_check_score >= 0.7) text-yellow-600 dark:text-yellow-400
                                    @else text-red-600 dark:text-red-400 @endif">
                                {{ $businessAsset->avg_data_quality_check_score !== null ? number_format($businessAsset->avg_data_quality_check_score * 100, 2) . '%' : '-' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Maximum Score') }}</p>
                            <p class="text-2xl font-bold @if($businessAsset->max_data_quality_check_score >= 0.9) text-green-600 dark:text-green-400
                                    @elseif($businessAsset->max_data_quality_check_score >= 0.7) text-yellow-600 dark:text-yellow-400
                                    @else text-red-600 dark:text-red-400 @endif">
                                {{ $businessAsset->max_data_quality_check_score !== null ? number_format($businessAsset->max_data_quality_check_score * 100, 2) . '%' : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Two-column layout -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left column (2/3 width) - main content area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Business Rules Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Business Rules') }} ({{ $businessAsset->businessRules->count() }})
                        </h2>
                    </div>
                    @if ($businessAsset->businessRules->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('No business rules associated with this business asset.') }}
                        </div>
                    @else
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($businessAsset->businessRules as $rule)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('web.business-rules.show', $rule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $rule->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $rule->description ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Data Issues Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Data Issues') }} ({{ $businessAsset->dataIssues->count() }})
                        </h2>
                    </div>
                    @if ($businessAsset->dataIssues->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('No data issues associated with this business asset.') }}
                        </div>
                    @else
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Description') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Created At') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($businessAsset->dataIssues as $dataIssue)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $dataIssue->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dataIssue->description ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $dataIssue->created_at->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Governance Score Details Section -->
                @if($businessAsset->governanceScore)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Governance Score Details') }}
                            </h2>
                        </div>
                        
                        <!-- Criteria List -->
                        <div class="p-6">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                                {{ __('Scoring Criteria') }}
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $criteria = App\Models\GovernanceCriterion::whereIn('key', array_keys($businessAsset->governanceScore->criteria_results ?? []))
                                        ->get()
                                        ->keyBy('key');
                                @endphp
                                @foreach($businessAsset->governanceScore->criteria_results as $key => $met)
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
                                                +{{ $businessAsset->governanceScore->criteria_weights[$key] ?? 0 }} pts
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Max Possible Score') }}: {{ $businessAsset->governanceScore->max_possible_score }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Governance Score History Section -->
                    @if($businessAsset->governanceScores->count() > 1)
                        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Governance Score History') }} ({{ $businessAsset->governanceScores->count() }})
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($businessAsset->governanceScores->take(10) as $score)
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            </div>
            
            <!-- Right column (1/3 width) - info cards -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Domain Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Domain') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->domain?->name ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Initiative Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Initiative') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataInitiative?->label ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Steward Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Steward') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataSteward()->first()?->name ?? '-' }}
                    </p>
                </div>
                
                <!-- Data Owner Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Data Owner') }}
                    </h3>
                    <p class="mt-2 text-gray-900 dark:text-gray-100">
                        {{ $businessAsset->dataOwner()->first()?->name ?? '-' }}
                    </p>
                </div>

                <!-- Governance Score Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Governance Score') }}
                    </h3>
                    <div class="mt-2">
                        @if($businessAsset->governanceScore)
                            <span class="text-3xl font-bold @if($businessAsset->governanceScore->score >= 0.8) text-green-600 dark:text-green-400
                                @elseif($businessAsset->governanceScore->score >= 0.5) text-yellow-600 dark:text-yellow-400
                                @else text-red-600 dark:text-red-400 @endif">
                                {{ number_format($businessAsset->governanceScore->score * 100, 1) }}%
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                ({{ number_format($businessAsset->governanceScore->score, 2) }}/1.0)
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-assets.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Assets') }}
            </a>
        </div>
    </div>
</x-layouts::app>
