<div class="space-y-8 p-6">
        {{-- Governance Section --}}
        <div>
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                {{ __('Governance') }}
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- Data Initiatives Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $dataInitiativesCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Data Initiatives') }}
                    </div>
                </div>

                {{-- Business Assets Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $businessAssetsCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Business Assets') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Quality Section --}}
        <div>
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100 mb-4">
                {{ __('Data Quality') }}
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                {{-- Business Rules Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $businessRulesCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Business Rules') }}
                    </div>
                </div>

                {{-- Data Quality Issues Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $dataIssuesCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Data Quality Issues') }}
                    </div>
                </div>

                {{-- Root Causes Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $rootCausesCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Root Causes') }}
                    </div>
                </div>

                {{-- Solutions Card --}}
                <div class="aspect-square rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 flex flex-col justify-center hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors">
                    <div class="text-4xl font-bold text-center text-neutral-900 dark:text-neutral-100">
                        {{ $solutionsCount }}
                    </div>
                    <div class="text-sm text-neutral-600 dark:text-neutral-400 text-center mt-2">
                        {{ __('Solutions') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
