@props([
    'url' => null,
    'message' => null,
])

{{-- Expects to be rendered inside an Alpine scope exposing `deleteOpen` (see catalog/actions and catalog/row-actions). --}}
<div
    x-show="deleteOpen"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('Confirm deletion') }}"
    @keydown.escape.window="deleteOpen = false"
>
    {{-- Overlay --}}
    <div class="fixed inset-0 bg-black/50" @click="deleteOpen = false"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-md rounded-xl border border-neutral-200 bg-white p-6 shadow-xl dark:border-neutral-700 dark:bg-zinc-800">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Confirm deletion') }}
        </h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $message ?? __('Are you sure you want to delete this item? This action cannot be undone.') }}
        </p>
        <div class="mt-6 flex justify-end gap-2">
            <flux:button variant="ghost" @click="deleteOpen = false">
                {{ __('Cancel') }}
            </flux:button>
            <form method="POST" action="{{ $url }}" x-ref="deleteForm">
                @csrf
                @method('DELETE')
                <flux:button variant="danger" icon="trash" @click="$refs.deleteForm.submit()">
                    {{ __('Delete') }}
                </flux:button>
            </form>
        </div>
    </div>
</div>
