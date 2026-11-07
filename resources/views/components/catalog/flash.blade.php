@if (session('success'))
    <flux:callout variant="success" icon="check-circle">
        {{ session('success') }}
    </flux:callout>
@endif
