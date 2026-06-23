<x-layouts::app :title="$dataInitiative->label">
    <div class="space-y-6">
        <!-- Header Section with Action Buttons -->
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $dataInitiative->label }}
                </h1>
                <div class="mt-2 flex items-center space-x-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Code') }}: {{ $dataInitiative->code }}</span>
                </div>
                @if($dataInitiative->description)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ $dataInitiative->description }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a 
                    href="{{ route('web.data-initiatives.edit', $dataInitiative) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-800 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('web.data-initiatives.destroy', $dataInitiative) }}" onsubmit="return confirm('{{ __("Are you sure you want to delete this data initiative?") }}')">
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

        <!-- Governance Score Summary -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Governance Score') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Current Average') }}</p>
                    <p class="text-2xl font-bold @if($dataInitiative->average_governance_score >= 0.8) text-green-600 dark:text-green-400
                            @elseif($dataInitiative->average_governance_score >= 0.5) text-yellow-600 dark:text-yellow-400
                            @else text-red-600 dark:text-red-400 @endif">
                        {{ $dataInitiative->average_governance_score !== null ? number_format($dataInitiative->average_governance_score * 100, 1) . '%' : '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Business Assets') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $dataInitiative->businessAssets->count() }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Score History Entries') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $dataInitiative->governanceScoreHistory->count() }}
                    </p>
                </div>
            </div>
            @if($dataInitiative->governanceScoreHistory->isNotEmpty())
                <div class="mt-4">
                    <a href="{{ route('web.data-initiatives.governance-score.history', $dataInitiative) }}" 
                       class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                        {{ __('View Governance Score History') }}
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Two-column layout -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left column (2/3 width) - main content area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Business Assets Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-zinc-700">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Business Assets') }} ({{ $dataInitiative->businessAssets->count() }})
                        </h2>
                    </div>
                    @if ($dataInitiative->businessAssets->isEmpty())
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('No business assets associated with this data initiative.') }}
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
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($dataInitiative->businessAssets as $businessAsset)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('web.business-assets.show', $businessAsset) }}" 
                                               class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $businessAsset->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($businessAsset->definition ?? '-', 50) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $businessAsset->domain?->name ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <!-- Right column (1/3 width) - sidebar info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Metadata Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Metadata') }}</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Created') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $dataInitiative->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $dataInitiative->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Roles Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Assigned Roles') }}</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Data Steward') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                                @if ($dataInitiative->dataSteward()->get()->isNotEmpty())
                                    @foreach ($dataInitiative->dataSteward()->get() as $steward)
                                        <div>{{ $steward->name }}</div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Data Owner') }}</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                                @if ($dataInitiative->dataOwner()->get()->isNotEmpty())
                                    @foreach ($dataInitiative->dataOwner()->get() as $owner)
                                        <div>{{ $owner->name }}</div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.data-initiatives.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Data Initiatives') }}
            </a>
        </div>
    </div>
</x-layouts::app>
