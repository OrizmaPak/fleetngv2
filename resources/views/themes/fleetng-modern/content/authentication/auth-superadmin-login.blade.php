@extends('layouts/fullLayoutMaster')
@section('title', 'Super Admin Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', ['portalTitle' => 'Super Admin Login', 'portalDescription' => 'Sign in to administer the FleetNG platform.', 'action' => route('auth-superadmin-login')])
@endsection
