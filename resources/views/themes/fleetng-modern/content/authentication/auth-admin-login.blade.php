@extends('layouts/fullLayoutMaster')
@section('title', 'Admin Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', ['portalTitle' => 'Admin Login', 'portalDescription' => 'Sign in to manage fleet operations.', 'action' => route('auth-admin-login')])
@endsection
