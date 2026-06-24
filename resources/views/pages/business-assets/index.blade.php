<x-layouts::app :title="__('Business Assets')">
    <div class="space-y-6">
        <!-- Header with New Button -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Assets') }}
            </h1>
            <a 
                href="{{ route('web.business-assets.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
            >
                {{ __('New') }}
            </a>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <form method="GET" action="{{ route('web.business-assets.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Search') }}
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="search" 
                                name="search" 
                                value="{{ request('search', '') }}"
                                placeholder="{{ __('Search by name or definition...') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @if(request('search'))
                                <button 
                                    type="button" 
                                    onclick="window.location.href='{{ route('web.business-assets.index') }}'"
                                    class="absolute right-2 top-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    title="{{ __('Clear search') }}"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Domain Filter -->
                    <div>
                        <label for="domain_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Domain') }}
                        </label>
                        <select 
                            id="domain_id" 
                            name="domain_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">{{ __('All Domains') }}</option>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->id }}" {{ request('domain_id') == $domain->id ? 'selected' : '' }}>
                                    {{ $domain->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Initiative Filter -->
                    <div>
                        <label for="data_initiative_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Data Initiative') }}
                        </label>
                        <select 
                            id="data_initiative_id" 
                            name="data_initiative_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">{{ __('All Initiatives') }}</option>
                            @foreach($dataInitiatives as $initiative)
                                <option value="{{ $initiative->id }}" {{ request('data_initiative_id') == $initiative->id ? 'selected' : '' }}>
                                    {{ $initiative->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Steward Filter -->
                    <div>
                        <label for="data_steward_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Data Steward') }}
                        </label>
                        <select 
                            id="data_steward_id" 
                            name="data_steward_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">{{ __('All Stewards') }}</option>
                            @foreach($dataStewards as $steward)
                                <option value="{{ $steward->id }}" {{ request('data_steward_id') == $steward->id ? 'selected' : '' }}>
                                    {{ $steward->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Owner Filter -->
                    <div>
                        <label for="data_owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Data Owner') }}
                        </label>
                        <select 
                            id="data_owner_id" 
                            name="data_owner_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">{{ __('All Owners') }}</option>
                            @foreach($dataOwners as $owner)
                                <option value="{{ $owner->id }}" {{ request('data_owner_id') == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Filter') }}
                    </button>
                    <a 
                        href="{{ route('web.business-assets.index') }}"
                        class="ml-2 inline-flex items-center px-4 py-2 bg-gray-500 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-600 dark:hover:bg-gray-500 focus:bg-gray-600 dark:focus:bg-gray-500 active:bg-gray-700 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                    >
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if ($businessAssets->isEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    @if(request()->hasAny(['search', 'domain_id', 'data_initiative_id', 'data_steward_id', 'data_owner_id']))
                        {{ __('No business assets found matching your criteria. Try adjusting your filters.') }}
                    @else
                        {{ __('No business assets found. Create one to get started.') }}
                    @endif
                </p>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Steward') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Data Owner') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Governance Score') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Avg DQ Score') }}
                            </th>

                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($businessAssets as $asset)
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
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $asset->dataSteward()->first()?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $asset->dataOwner()->first()?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($asset->governanceScore)
                                        <span class="@if($asset->governanceScore->score >= 0.8) text-green-600 dark:text-green-400
                                            @elseif($asset->governanceScore->score >= 0.5) text-yellow-600 dark:text-yellow-400
                                            @else text-red-600 dark:text-red-400 @endif font-medium">
                                            {{ number_format($asset->governanceScore->score * 100, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($asset->avg_data_quality_check_score !== null)
                                        <span class="@if($asset->avg_data_quality_check_score >= 0.9) text-green-600 dark:text-green-400
                                            @elseif($asset->avg_data_quality_check_score >= 0.7) text-yellow-600 dark:text-yellow-400
                                            @else text-red-600 dark:text-red-400 @endif font-medium">
                                            {{ number_format($asset->avg_data_quality_check_score * 100, 2) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $businessAssets->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
