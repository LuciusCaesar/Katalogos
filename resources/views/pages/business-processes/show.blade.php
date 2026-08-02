<x-layouts::app :title="__('Business Process Details')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Business Process Details') }}
            </h1>
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('web.business-processes.edit', $businessProcess) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-800 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition ease-in-out duration-150"
                >
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('web.business-processes.destroy', $businessProcess) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this business process?') }}');">
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

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-md p-4">
                <p class="text-green-800 dark:text-green-200 text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Details Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ $businessProcess->name }}</h2>

            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Name') }}</h3>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProcess->name }}</p>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Description') }}</h3>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProcess->description ?? '-' }}</p>
                </div>

                <!-- Business Assets -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Business Assets') }}</h3>
                    @if ($businessProcess->businessAssets->isEmpty())
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('No business assets associated.') }}</p>
                    @else
                        <div class="mt-2 bg-gray-50 dark:bg-zinc-700 rounded border border-gray-200 dark:border-zinc-600 p-4">
                            <ul class="space-y-2">
                                @foreach ($businessProcess->businessAssets as $businessAsset)
                                    <li class="text-sm">
                                        <a href="{{ route('web.business-assets.show', $businessAsset) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            {{ $businessAsset->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Meta -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Created At') }}</h3>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProcess->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Updated At') }}</h3>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProcess->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div>
            <a href="{{ route('web.business-processes.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ __('Back to Business Processes') }}
            </a>
        </div>
    </div>
</x-layouts::app>
