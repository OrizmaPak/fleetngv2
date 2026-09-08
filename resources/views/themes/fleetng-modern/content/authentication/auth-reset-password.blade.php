@extends('layouts/fullLayoutMaster')
@section('title', 'Reset Password')
@section('content')
<div class="auth-wrapper auth-v2"><div class="auth-inner row m-0">
    <a class="brand-logo" href="{{ url('/') }}"><img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG"></a>
    <div class="d-flex align-items-center auth-bg"><div class="w-100">
        <p class="fleetng-eyebrow">Account recovery</p>
        <h1 class="card-title mb-1">Choose a new password</h1>
        <p class="card-text mb-2">Use a strong password that you have not used for this account before.</p>
        @if(Session::get('error_message'))<div class="alert alert-danger" role="alert">{{ Session::get('error_message') }}</div>@endif
        @if(Session::get('success_message'))<div class="alert alert-success" role="status">{{ Session::get('success_message') }}</div>@endif
        <form action="{{ url('/reset-password/' . $dbresponse['token']) }}" method="POST">
            @csrf
            <div class="form-group"><label for="reset-password-new">New password</label><div class="input-group form-password-toggle"><input class="form-control" id="reset-password-new" type="password" name="password" autocomplete="new-password" placeholder="Enter new password" required autofocus><div class="input-group-append"><button class="input-group-text" type="button" aria-label="Show password"><i data-feather="eye"></i></button></div></div></div>
            <div class="form-group"><label for="reset-password-confirm">Confirm new password</label><div class="input-group form-password-toggle"><input class="form-control" id="reset-password-confirm" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Re-enter new password" required><div class="input-group-append"><button class="input-group-text" type="button" aria-label="Show password"><i data-feather="eye"></i></button></div></div></div>
            <input type="hidden" name="type" value="{{ $type }}">
            <button type="submit" class="btn btn-primary btn-block">Update password</button>
        </form>
    </div></div>
</div></div>
@endsection
