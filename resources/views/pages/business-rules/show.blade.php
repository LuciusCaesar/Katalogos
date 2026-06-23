<x-layouts::app :title="$businessRule->name">
    <div class="space-y-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $businessRule->name }}
                </h1>
                @if ($businessRule->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $businessRule->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.business-rules.edit', $businessRule) }}"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.business-rules.destroy', $businessRule) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this business rule?') }}')">
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
                    {{ __('Business Assets') }} ({{ $businessRule->businessAssets->count() }})
                </h2>
            </div>
            @if ($businessRule->businessAssets->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No business assets associated with this business rule.') }}
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
                        @foreach ($businessRule->businessAssets as $asset)
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

        <!-- Data Issues Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Data Issues') }} ({{ $businessRule->dataIssues->count() }})
                </h2>
            </div>
            @if ($businessRule->dataIssues->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No data issues associated with this business rule.') }}
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
                        @foreach ($businessRule->dataIssues as $dataIssue)
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

        <!-- Data Quality Checks Section -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Data Quality Checks') }} ({{ $businessRule->dataQualityChecks->count() }})
                </h2>
            </div>
            @if ($businessRule->dataQualityChecks->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    {{ __('No data quality checks associated with this business rule.') }}
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
                                {{ __('Score') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Sources') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Created At') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($businessRule->dataQualityChecks as $dataQualityCheck)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('web.data-quality-checks.show', $dataQualityCheck) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $dataQualityCheck->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataQualityCheck->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm @if($dataQualityCheck->latestScore && $dataQualityCheck->latestScore->score >= 0.9) text-green-600 dark:text-green-400
                                        @elseif($dataQualityCheck->latestScore && $dataQualityCheck->latestScore->score >= 0.7) text-yellow-600 dark:text-yellow-400
                                        @else text-red-600 dark:text-red-400 @endif">
                                    {{ $dataQualityCheck->latestScore?->score_percentage ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataQualityCheck->dataSources->pluck('name')->implode(', ') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataQualityCheck->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="pt-4">
            <a href="{{ route('web.business-rules.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Rules') }}
            </a>
        </div>
    </div>
</x-layouts::app>
