@props([
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMessage' => null,
    'diagramUrl' => null,
    'teamUrl' => null,
])

<div class="flex flex-wrap items-center justify-end gap-2">
    @if ($diagramUrl)
        <flux:button :href="$diagramUrl" variant="ghost" icon="share">
            {{ __('View impact diagram') }}
        </flux:button>
    @endif
    @if ($teamUrl)
        <flux:button :href="$teamUrl" variant="ghost" icon="users">
            {{ __('Manage Team') }}
        </flux:button>
    @endif
    @if ($editUrl)
        <flux:button :href="$editUrl" variant="primary" icon="pencil">
            {{ __('Edit') }}
        </flux:button>
    @endif
    @if ($deleteUrl)
        <div x-data="{ deleteOpen: false }" class="inline-flex">
            <flux:button variant="danger" icon="trash" @click="deleteOpen = true">
                {{ __('Delete') }}
            </flux:button>
            <x-catalog.delete-modal :url="$deleteUrl" :message="$deleteMessage" />
        </div>
    @endif
</div>
