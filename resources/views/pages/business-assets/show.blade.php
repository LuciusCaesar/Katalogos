<x-layouts::app :title="$businessAsset->name">
    <x-catalog.show-layout
        :title="$businessAsset->name"
        type-label="__('Business Asset')"
        :breadcrumbs="[
            ['label' => __('Business Assets'), 'href' => route('web.business-assets.index')],
            ['label' => $businessAsset->name, 'href' => null],
        ]"
        :definition="$businessAsset->definition"
        :edit-url="route('web.business-assets.edit', $businessAsset)"
        :delete-url="route('web.business-assets.destroy', $businessAsset)"
        :delete-message="__('Are you sure you want to delete this business asset?')"
        :diagram-url="route('web.impact-diagram.show', ['entityType' => 'business-asset', 'entity' => $businessAsset])"
        :team-url="route('web.business-assets.team', $businessAsset)"
        :back-url="route('web.business-assets.index')"
        back-label="__('Back to Business Assets')"
    >
        <x-slot:responsibilities>
            <x-catalog.responsibilities
                :groups="[
                    ['role' => __('Data Steward'), 'users' => $businessAsset->dataSteward],
                    ['role' => __('Data Owner'), 'users' => $businessAsset->dataOwner],
                ]"
            />
        </x-slot:responsibilities>

        <x-slot:metrics>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <x-catalog.metric-card :label="__('Governance Score')">
                        <x-catalog.score :value="$businessAsset->governanceScore?->score" variant="governance" />
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Avg DQ Score')">
                        <x-catalog.score :value="$businessAsset->avg_data_quality_check_score" :decimals="2" />
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Min DQ Score')">
                        <x-catalog.score :value="$businessAsset->min_data_quality_check_score" :decimals="2" />
                    </x-catalog.metric-card>
                    <x-catalog.metric-card :label="__('Max DQ Score')">
                        <x-catalog.score :value="$businessAsset->max_data_quality_check_score" :decimals="2" />
                    </x-catalog.metric-card>
                </div>
                @if ($businessAsset->governanceScore)
                    <div class="flex gap-4 text-sm">
                        <a href="{{ route('web.business-assets.governance-score.details', $businessAsset) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('Details') }}
                        </a>
                        <a href="{{ route('web.business-assets.governance-score.history', $businessAsset) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('History') }}
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:metrics>

        {{-- Business Rules --}}
        <x-catalog.section
            :title="__('Business Rules')"
            :count="$businessAsset->businessRules->count()"
            :empty-message="__('No business rules associated with this business asset.')"
            :empty="$businessAsset->businessRules->isEmpty()"
        >
            <x-catalog.table :columns="[__('Name'), __('Description')]">
                @foreach ($businessAsset->businessRules as $rule)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('web.business-rules.show', $rule) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $rule->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $rule->description ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </x-catalog.table>
        </x-catalog.section>

        {{-- Data Issues --}}
        <x-catalog.section
            :title="__('Data Issues')"
            :count="$businessAsset->dataIssues->count()"
            :empty-message="__('No data issues associated with this business asset.')"
            :empty="$businessAsset->dataIssues->isEmpty()"
        >
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($businessAsset->dataIssues as $dataIssue)
                    <div x-data="{ open: false }" class="p-6">
                        <div class="flex items-start">
                            <button
                                @click="open = !open"
                                class="mr-3 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 transition-colors hover:bg-gray-200 dark:bg-zinc-700 dark:hover:bg-zinc-600"
                                aria-label="{{ __('Toggle root causes') }}"
                            >
                                <svg x-show="!open" class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <svg x-show="open" class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                </svg>
                            </button>
                            <div class="flex-1">
                                <a href="{{ route('web.data-issues.show', $dataIssue) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ $dataIssue->name }}
                                    </h3>
                                </a>
                                <span class="ml-3 mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataIssue->created_at->format('Y-m-d H:i') }}
                                </span>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $dataIssue->description ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @if ($dataIssue->rootCauses->isNotEmpty())
                            <div x-show="open" x-transition x-collapse class="mt-4 ml-9 space-y-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Root Causes') }}:
                                </h4>
                                @foreach ($dataIssue->rootCauses as $rootCause)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="flex items-center gap-2">
                                            @if ($rootCause->dimension)
                                                @php
                                                    $icon = match ($rootCause->dimension->value) {
                                                        'People' => 'user-group',
                                                        'Process' => 'cog-6-tooth',
                                                        'Tool' => 'wrench-screwdriver',
                                                        default => 'puzzle-piece',
                                                    };
                                                @endphp
                                                <flux:icon name="{{ $icon }}" variant="outline" class="h-4 w-4 text-blue-500" />
                                            @endif
                                            <a href="{{ route('web.root-causes.show', $rootCause) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ $rootCause->name }}
                                            </a>
                                        </div>
                                        <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $rootCause->description ?? '-' }}
                                        </p>
                                        @if ($rootCause->solutions->isNotEmpty())
                                            <h6 class="mt-4 mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Solutions') }}:
                                            </h6>
                                            <div class="ml-4 space-y-2">
                                                @foreach ($rootCause->solutions as $solution)
                                                    <div class="rounded bg-gray-50 p-3 dark:bg-zinc-700">
                                                        <div class="flex items-center gap-2">
                                                            @if ($solution->dimension)
                                                                @php
                                                                    $solutionIcon = match ($solution->dimension->value) {
                                                                        'People' => 'user-group',
                                                                        'Process' => 'cog-6-tooth',
                                                                        'Tool' => 'wrench-screwdriver',
                                                                        default => 'check-circle',
                                                                    };
                                                                @endphp
                                                                <flux:icon name="{{ $solutionIcon }}" variant="outline" class="h-4 w-4 text-green-500" />
                                                            @endif
                                                            <a href="{{ route('web.solutions.show', $solution) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                                {{ $solution->name }}
                                                            </a>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $solution->description ?? '-' }}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm italic text-gray-400 dark:text-gray-500">
                                                {{ __('No solutions defined for this root cause.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-catalog.section>

        <x-slot:aside>
            <x-catalog.detail-card :title="__('Domain')">
                @if ($businessAsset->domain)
                    <a href="{{ route('web.domains.show', $businessAsset->domain) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                        {{ $businessAsset->domain->name }}
                    </a>
                @else
                    <span class="text-gray-400 dark:text-gray-500">-</span>
                @endif
            </x-catalog.detail-card>

            <x-catalog.detail-card :title="__('Data Initiative')">
                @if ($businessAsset->dataInitiative)
                    <a href="{{ route('web.data-initiatives.show', $businessAsset->dataInitiative) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                        {{ $businessAsset->dataInitiative->label }}
                    </a>
                @else
                    <span class="text-gray-400 dark:text-gray-500">-</span>
                @endif
            </x-catalog.detail-card>

            <x-catalog.detail-card :title="__('Categorization Level')">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('Consistency') }}
                        </p>
                        <p class="mt-1 font-medium">
                            {{ ucfirst($businessAsset->consistency ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('Integrity') }}
                        </p>
                        <p class="mt-1 font-medium">
                            {{ ucfirst($businessAsset->integrity ?? '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('Availability') }}
                        </p>
                        <p class="mt-1 font-medium">
                            {{ ucfirst($businessAsset->availability ?? '-') }}
                        </p>
                    </div>
                </div>
            </x-catalog.detail-card>
        </x-slot:aside>

        <x-slot:meta>
            <x-catalog.timestamps :model="$businessAsset" />
        </x-slot:meta>
    </x-catalog.show-layout>
</x-layouts::app>
