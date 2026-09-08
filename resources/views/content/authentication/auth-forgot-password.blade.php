@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Forgot Password')

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
      <img src="{{asset('images/logo/fleetng-logo.svg')}}" alt="FleetNG Logo" width="100">
    </a>
    <!-- /Brand logo-->
    <!-- Left Text-->
    <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
      <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
        @if($configData['theme'] === 'dark')
        <img class="img-fluid" src="{{asset('images/pages/forgot-password-v2-dark.svg')}}" alt="Forgot password V2" />
        @else
        <img class="img-fluid" src="{{asset('images/pages/forgot-password-v2.svg')}}" alt="Forgot password V2" />
        @endif
      </div>
    </div>
    <!-- /Left Text-->
    <!-- Forgot password-->
    <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
      <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
        <h2 class="card-title font-weight-bold mb-1">Forgot Password? 🔒</h2>
        <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>
        @if(Session::get('error_message'))
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
        @if(Session::get('success_message'))
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
        <form class="auth-forgot-password-form mt-2" action="{{URL::to('forgot-password')}}" method="POST">
          @csrf
          <div class="form-group">
            <label class="form-label" for="forgot-password-email">Email</label>
            <input class="form-control" id="forgot-password-email" type="text" name="email" placeholder="john@example.com" aria-describedby="forgot-password-email" autofocus="" tabindex="1" />
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="2">Send reset link</button>
        </form>
        <p class="text-center mt-2">
          <a href="{{url('/admin')}}">
            <i data-feather="chevron-left"></i> Back to login
          </a>
        </p>
      </div>
    </div>
    <!-- /Forgot password-->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
@endsection

@section('page-script')
<script src="{{asset(mix('js/scripts/pages/page-auth-forgot-password.js'))}}"></script>
@endsection