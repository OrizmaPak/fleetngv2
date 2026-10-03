@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php $configData = Helper::applClasses(); @endphp
<!DOCTYPE html>
<html lang="{{ session('locale', $configData['defaultLanguage']) }}" data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}" data-fleetng-theme="light">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#377df6">
    <script>
        (function () {
            var saved = localStorage.getItem('fleetng-colour-theme');
            var preferred = 'light';
            document.documentElement.setAttribute('data-fleetng-theme', saved || preferred);
        }());
    </script>
    <title>@yield('title') - FleetNG</title>
    <link rel="icon" href="{{ asset('front/images/favicon.png') }}" type="image/png">
    @php
        $routeName = optional(request()->route())->getName();
        $loginPreloadImage = null;

        if ($routeName === 'auth-superadmin-login') {
            $loginPreloadImage = 'themes/fleetng-modern/images/login/platform-control.png';
        } elseif ($routeName === 'auth-payment-user-login') {
            $loginPreloadImage = 'themes/fleetng-modern/images/login/payment-operations.png';
        } elseif (in_array($routeName, ['auth-login', 'auth-admin-login', 'auth-user-login'], true)) {
            $loginPreloadImage = 'themes/fleetng-modern/images/login/operations-command.png';
        }
    @endphp
    @if($loginPreloadImage)
        <link rel="preload" as="image" href="{{ asset($loginPreloadImage) }}" fetchpriority="high">
        <link rel="preload" as="image" href="{{ asset('images/logo/fleetng-logo.svg') }}">
    @endif
    @include('panels.styles')
</head>
<body class="fleetng-modern fleetng-modern-auth {{ $configData['bodyClass'] }}" data-framework="laravel" data-asset-path="{{ asset('/') }}">
    <main class="fleetng-auth-canvas">
        @yield('content')
    </main>
    @include('panels.scripts')
</body>
</html>
