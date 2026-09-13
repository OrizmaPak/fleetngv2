<div class="auth-wrapper auth-v2">
    <div class="auth-inner row m-0">
        <a class="brand-logo" href="{{ url('/') }}"><img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG"></a>
        <div class="d-flex align-items-center auth-bg">
            <div class="w-100">
                <p class="fleetng-eyebrow">Secure operations portal</p>
                <h1 class="card-title mb-1">{{ $portalTitle }}</h1>
                <p class="card-text mb-2">{{ $portalDescription }}</p>

                @if(Session::get('error_message'))<div class="alert alert-danger" role="alert">{{ Session::get('error_message') }}</div>@endif
                @if(Session::get('success_message'))<div class="alert alert-success" role="status">{{ Session::get('success_message') }}</div>@endif
                @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif

                <form class="auth-login-form mt-2" action="{{ $action }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input class="form-control" id="email" type="email" name="email" autocomplete="username" placeholder="name@company.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between">
                            <label for="password">Password</label>
                            <a href="{{ route('auth-forgot-password') }}"><small>Forgot password?</small></a>
                        </div>
                        <div class="input-group input-group-merge fleetng-password-toggle">
                            <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
                            <div class="input-group-append"><button class="input-group-text cursor-pointer" type="button" aria-label="Show password"><i data-feather="eye"></i></button></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" id="remember-me" type="checkbox" name="remember_me" value="remember">
                            <label class="custom-control-label" for="remember-me">Remember me</label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                </form>

                <div class="fleetng-portal-links" aria-label="Other sign-in options">
                    <a href="{{ url('/') }}">Back to homepage</a>
                    <a href="{{ url('customer-portal') }}">Customer portal</a>
                </div>
                <p class="fleetng-auth-security"><i data-feather="shield"></i> Protected FleetNG access</p>
            </div>
        </div>
    </div>
</div>
