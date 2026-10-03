<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#050b18">
    <title>Customer Portal | FleetNG</title>
    <link rel="icon" href="{{ asset('images/logo/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('customer-preview/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('customer-preview/login.css') }}?v=20260924-customer-loading">
    @php
        $customerPortalTestOtpEnabled = filter_var(env('CUSTOMER_PORTAL_TEST_OTP', false), FILTER_VALIDATE_BOOLEAN) || app()->environment(['local', 'testing']);
    @endphp
    <script>
        window.CustomerPortalConfig = {
            logoUrl: @json(asset('images/logo/fleetng-logo.svg')),
            loginImageUrl: @json(asset('front/images/hero-img.png')),
            homeUrl: @json(url('/')),
            apiBaseUrl: @json(url('customer-portal/api')),
            titleSuffix: 'FleetNG',
            testOtpEnabled: {{ $customerPortalTestOtpEnabled ? 'true' : 'false' }}
        };
    </script>
    <script defer src="{{ asset('vendors/js/feather-icons/feather-icons.min.js') }}"></script>
    <script defer src="{{ asset('customer-preview/live-data.js') }}?v=f634954"></script>
    <script defer src="{{ asset('customer-preview/app.js') }}?v=20260925-customer-nav"></script>
</head>
<body>
    <a class="skip" href="#main">Skip to content</a>
    <div id="app"></div>
    <dialog id="dialog" aria-labelledby="dialog-title"></dialog>
    <div id="toast" role="status" aria-live="polite"></div>
</body>
</html>
