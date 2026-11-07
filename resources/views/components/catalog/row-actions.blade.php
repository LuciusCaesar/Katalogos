@props([
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMessage' => null,
    'teamUrl' => null,
])

@php
    $buttonClass = 'rounded p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-zinc-700 dark:hover:text-blue-400';
    $deleteButtonClass = 'rounded p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-red-600 dark:text-gray-400 dark:hover:bg-zinc-700 dark:hover:text-red-400';
@endphp

<td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
    <div class="flex items-center justify-end gap-1">
        @if ($viewUrl)
            <a href="{{ $viewUrl }}" title="{{ __('View') }}" aria-label="{{ __('View') }}" class="{{ $buttonClass }}">
                <flux:icon icon="eye" variant="outline" class="size-4" />
            </a>
        @endif
        @if ($teamUrl)
            <a href="{{ $teamUrl }}" title="{{ __('Team') }}" aria-label="{{ __('Team') }}" class="{{ $buttonClass }}">
                <flux:icon icon="users" variant="outline" class="size-4" />
            </a>
        @endif
        @if ($editUrl)
            <a href="{{ $editUrl }}" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}" class="{{ $buttonClass }}">
                <flux:icon icon="pencil" variant="outline" class="size-4" />
            </a>
        @endif
        @if ($deleteUrl)
            <div x-data="{ deleteOpen: false }" class="inline-flex">
                <button type="button" @click="deleteOpen = true" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}" class="{{ $deleteButtonClass }}">
                    <flux:icon icon="trash" variant="outline" class="size-4" />
                </button>
                <x-catalog.delete-modal :url="$deleteUrl" :message="$deleteMessage" />
            </div>
        @endif
    </div>
</td>
