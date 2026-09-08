@php
    $configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Reset Password')

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-v2">
        <div class="auth-inner row m-0">
            <!-- Brand logo-->
            <a class="brand-logo" href="javascript:void(0);">
                <img src="{{ asset('images/logo/fleetng-logo.svg') }}" alt="FleetNG Logo" width="100">
            </a>
            <!-- /Brand logo-->
            <!-- Left Text-->
            <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
                    @if ($configData['theme'] === 'dark')
                        <img src="{{ asset('images/pages/reset-password-v2-dark.svg') }}" class="img-fluid"
                            alt="Register V2" />
                    @else
                        <img src="{{ asset('images/pages/reset-password-v2.svg') }}" class="img-fluid" alt="Register V2" />
                    @endif
                </div>
            </div>
            <!-- /Left Text-->
            <!-- Reset password-->
            <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                    <h2 class="card-title font-weight-bold mb-1">Reset Password 🔒</h2>
                    <p class="card-text mb-2">Your new password must be different from previously used passwords</p>


                    @if (Session::get('error_message'))
                        <div class="demo-spacing-0">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="alert-body">
                                    {{ Session::get('error_message') }}
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    @if (Session::get('success_message'))
                        <div class="demo-spacing-0">
                            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                <div class="alert-body">
                                    {{ Session::get('success_message') }}
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    <form class="auth-reset-password-form mt-2"
                        action="{{ url('/reset-password/' . $dbresponse['token']) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="reset-password-new">New Password</label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input class="form-control form-control-merge" id="reset-password-new" type="password"
                                    name="password" placeholder="············" aria-describedby="reset-password-new"
                                    autofocus="" tabindex="1" />
                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer">
                                        <i data-feather="eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="reset-password-confirm">Confirm Password</label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input class="form-control form-control-merge" id="reset-password-confirm" type="password"
                                    name="password_confirmation" placeholder="············"
                                    aria-describedby="reset-password-confirm" tabindex="2" />
                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" tabindex="3">Set New Password</button>
                        <input type="text" name="type" value="{{ $type }}" hidden>
                    </form>
                    <p class="text-center mt-2">
                        @if ($type === '1')
                            <a href="{{ url('/superadmin') }}">
                                <i data-feather="chevron-left"></i> Back to login
                            </a>
                        @elseif ($type === '4')
                            <a href="{{ url('/merchant') }}">
                                <i data-feather="chevron-left"></i> Back to login
                            </a>
                        @else
                            <a href="{{ url('/user') }}">
                                <i data-feather="chevron-left"></i> Back to login
                            </a>
                        @endif
                    </p>
                </div>
            </div>
            <!-- /Reset password-->
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset(mix('js/scripts/pages/page-auth-reset-password.js')) }}"></script>
@endsection