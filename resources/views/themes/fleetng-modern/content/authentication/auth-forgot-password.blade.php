@extends('layouts/fullLayoutMaster')
@section('title', 'Forgot Password')
@section('content')
<div class="auth-wrapper auth-v2"><div class="auth-inner row m-0">
    <a class="brand-logo" href="{{ url('/') }}"><img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG"></a>
    <div class="d-flex align-items-center auth-bg"><div class="w-100">
        <p class="fleetng-eyebrow">Account recovery</p>
        <h1 class="card-title mb-1">Reset your password</h1>
        <p class="card-text mb-2">Enter your account email and we will send password reset instructions.</p>
        @if(Session::get('error_message'))<div class="alert alert-danger" role="alert">{{ Session::get('error_message') }}</div>@endif
        @if(Session::get('success_message'))<div class="alert alert-success" role="status">{{ Session::get('success_message') }}</div>@endif
        <form action="{{ route('auth-forgot-password') }}" method="POST">
            @csrf
            <div class="form-group"><label for="forgot-password-email">Email address</label><input class="form-control" id="forgot-password-email" type="email" name="email" autocomplete="email" placeholder="name@company.com" value="{{ old('email') }}" required autofocus></div>
            <button type="submit" class="btn btn-primary btn-block">Send reset link</button>
        </form>
        <p class="text-center mt-2"><a href="{{ route('auth-admin-login') }}"><i data-feather="chevron-left"></i> Back to login</a></p>
    </div></div>
</div></div>
@endsection
