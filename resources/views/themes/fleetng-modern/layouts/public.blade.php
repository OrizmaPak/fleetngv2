<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#07142b">
    <title>@yield('title') - FleetNG</title>
    <link rel="icon" href="{{ asset('front/images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('themes/fleetng-modern/css/public.css') }}?v=20260924-loading">
</head>
<body class="fleetng-public">
    <header class="public-header">
        <a class="public-brand" href="{{ url('/') }}"><img src="{{ asset('front/images/logo/fleetng-logo.svg') }}" alt="FleetNG"><span>FLEETNG</span></a>
        <button class="public-menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false">Menu</button>
        <nav class="public-nav" aria-label="Main navigation">
            <a href="{{ url('/') }}#services">Services</a>
            <a href="{{ url('/') }}#about">About</a>
            <a href="{{ url('contact-us') }}">Contact</a>
            <a class="public-signin" href="{{ auth()->check() ? url('analytics') : route('auth-user-login') }}">Sign in</a>
        </nav>
    </header>
    <main>@yield('content')</main>
    <footer class="public-footer">
        <a class="public-brand" href="{{ url('/') }}"><img src="{{ asset('front/images/logo/fleetng-logo.svg') }}" alt=""><span>FLEETNG</span></a>
        <nav aria-label="Legal links">
            <a href="{{ url('contact-us') }}">Contact</a>
            <a href="{{ url('refund-policy') }}">Refund policy</a>
            <a href="{{ url('privacy-policy') }}">Privacy policy</a>
            <a href="{{ url('terms-conditions') }}">Terms</a>
        </nav>
        <p>&copy; {{ date('Y') }} FleetNG. All rights reserved.</p>
    </footer>
    <script src="{{ asset('themes/fleetng-modern/js/public.js') }}?v=20260924-loading" defer></script>
</body>
</html>
