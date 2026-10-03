@php
    $loginImage = $loginImage ?? 'themes/fleetng-modern/images/login/operations-command.png';
    $loginImageAlt = $loginImageAlt ?? 'FleetNG operations workspace';
    $visualKicker = $visualKicker ?? 'Operations command';
    $visualTitle = $visualTitle ?? 'Run fleet work from one controlled workspace.';
    $visualDescription = $visualDescription ?? 'Sign in to manage trips, vehicles, customers, payments, tracking, reports, and day-to-day fleet operations.';
    $visualFootPrimary = $visualFootPrimary ?? 'FleetNG V2';
    $visualFootSecondary = $visualFootSecondary ?? 'Secure role-based access';
@endphp

<div class="fleetng-login-shell">
    <section class="fleetng-login-visual" aria-label="{{ $loginImageAlt }}">
        <img class="fleetng-login-photo" src="{{ asset($loginImage) }}" alt="{{ $loginImageAlt }}">
        <div class="fleetng-login-visual-content">
            <a class="fleetng-login-brand" href="{{ url('/') }}" aria-label="FleetNG homepage">
                <img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="">
                <span>FleetNG</span>
            </a>
            <div class="fleetng-login-visual-message">
                <span>{{ $visualKicker }}</span>
                <h2>{{ $visualTitle }}</h2>
                <p>{{ $visualDescription }}</p>
            </div>
            <div class="fleetng-login-visual-foot">
                <span>{{ $visualFootPrimary }}</span>
                <span>{{ $visualFootSecondary }}</span>
            </div>
        </div>
    </section>

    <section class="fleetng-login-panel" aria-labelledby="fleetng-login-title">
        <div class="fleetng-login-panel-top">
            <span class="fleetng-login-mark">FLEET<span>/</span>NG</span>
            <a class="fleetng-login-home" href="{{ url('/') }}">
                <i data-feather="arrow-left"></i>
                <span>Homepage</span>
            </a>
        </div>

        <div class="fleetng-login-center">
            <div class="fleetng-login-form-head">
                <p class="fleetng-login-eyebrow">Secure operations portal</p>
                <h1 id="fleetng-login-title">{{ $portalTitle }}</h1>
                <p>{{ $portalDescription }}</p>
            </div>

            @if(Session::get('error_message'))<div class="alert alert-danger" role="alert">{{ Session::get('error_message') }}</div>@endif
            @if(Session::get('success_message'))<div class="alert alert-success" role="status">{{ Session::get('success_message') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif

            <form class="auth-login-form" action="{{ $action }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input class="form-control" id="email" type="email" name="email" autocomplete="username" placeholder="name@company.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <div class="fleetng-login-label-row">
                        <label for="password">Password</label>
                        <a href="{{ route('auth-forgot-password') }}">Forgot password?</a>
                    </div>
                    <div class="input-group input-group-merge fleetng-password-toggle">
                        <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
                        <div class="input-group-append">
                            <button class="input-group-text cursor-pointer" type="button" aria-label="Show password"><i data-feather="eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="fleetng-login-options">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="remember-me" type="checkbox" name="remember_me" value="remember">
                        <label class="custom-control-label" for="remember-me">Remember me</label>
                    </div>
                    <span>Protected access</span>
                </div>
                <button class="btn btn-primary btn-block" type="submit">
                    <span>Sign in</span>
                    <i data-feather="arrow-right"></i>
                </button>
            </form>

            <div class="fleetng-portal-links" aria-label="Other sign-in options">
                <a href="{{ url('customer-portal') }}">Customer portal</a>
                <a href="{{ url('/') }}">Back to homepage</a>
            </div>
            <p class="fleetng-auth-security"><i data-feather="shield"></i> Protected FleetNG access</p>
        </div>

        <div class="fleetng-login-panel-foot">
            <span>FLEETNG</span>
            <span>Role-aware sign in</span>
        </div>
    </section>
</div>
