@php
$configData = Helper::applClasses();
@endphp
@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
@endsection

@section('content')

<?php 

if (isset($_COOKIE['user_cookies']) && !empty($_COOKIE['user_cookies'])) {
    $data = unserialize($_COOKIE['user_cookies']);
    $email = $data['email'];
    $pswd = $data['pswd'];
} else {
    $email = '';
    $pswd = '';
}  

?>  

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
          <img class="img-fluid" src="{{asset('images/pages/login-v2-dark.svg')}}" alt="Login V2" />
          @else
          <img class="img-fluid" src="{{asset('images/pages/login-v2.svg')}}" alt="Login V2" />
          @endif
        </div>
      </div>
      <!-- /Left Text-->
      <!-- Login-->
      <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
        <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
          <h2 class="card-title font-weight-bold mb-1">Welcome to fleetNG Admin! &#x1F44B;</h2>
          <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>
           
          
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
          <form class="auth-login-form mt-2" action="{{URL::to('/admin')}}" method="POST">
            @csrf
            <div class="form-group">
              <label class="form-label" for="email">Email</label>
              <input class="form-control" id="email" type="email" name="email" placeholder="john@example.com" aria-describedby="email" autofocus="" tabindex="1" value="{{$email}}"/>
            </div>
            <div class="form-group">
              <div class="d-flex justify-content-between">
                <label for="password">Password</label>
                <a href="{{url('forgot-password')}}">
                  <small>Forgot Password?</small>
                </a>
              </div>
              <div class="input-group input-group-merge form-password-toggle">
                <input class="form-control form-control-merge" id="password" type="password" name="password" placeholder="············" aria-describedby="password" tabindex="2" value="{{$pswd}}"/>
                <div class="input-group-append">
                  <span class="input-group-text cursor-pointer">
                    <i data-feather="eye"></i>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div div class="custom-control custom-checkbox">
                <input class="custom-control-input" id="remember-me" type="checkbox" tabindex="3" name="remember_me" @if($email!='') checked @endif value="remember"/>
                <label class="custom-control-label" for="remember-me">Remember Me</label>
              </div>
            </div>
            <button class="btn btn-primary btn-block" tabindex="4">Sign in</button>
          </form>
      </div>
    </div>
    <!-- /Login-->
  </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
@endsection

@section('page-script')
<script src="{{asset(mix('js/scripts/pages/page-auth-login.js'))}}"></script>
@endsection