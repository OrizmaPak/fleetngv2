@props(['title', 'message' => '', 'icon' => 'inbox'])
<div class="fleetng-empty-state" role="status">
    <i data-feather="{{ $icon }}" aria-hidden="true"></i>
    <h2>{{ $title }}</h2>
    <p>{{ $message }}</p>
    {{ $slot }}
</div>
