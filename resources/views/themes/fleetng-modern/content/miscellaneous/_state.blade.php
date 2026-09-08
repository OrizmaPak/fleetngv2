<div class="fleetng-state-page">
    <img src="{{ asset('front/images/logo/fleetng-logo.svg') }}" alt="FleetNG" class="fleetng-state-logo">
    <div class="fleetng-state-icon"><i data-feather="{{ $icon }}"></i></div>
    <p class="fleetng-eyebrow">{{ $eyebrow }}</p>
    <h1>{{ $heading }}</h1>
    <p>{{ $message }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">Return home</a>
</div>
