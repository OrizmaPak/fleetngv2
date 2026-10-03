@extends('layouts/fullLayoutMaster')
@section('title', 'Admin Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', [
        'portalTitle' => 'Admin Login',
        'portalDescription' => 'Sign in to manage fleet operations.',
        'action' => route('auth-admin-login'),
        'loginImage' => 'themes/fleetng-modern/images/login/operations-command.png',
        'loginImageAlt' => 'FleetNG admin operations dashboard',
        'visualKicker' => 'Admin control',
        'visualTitle' => 'Manage fleet operations with dispatch-level visibility.',
        'visualDescription' => 'Review trips, drivers, clients, payments, reports, and operational activity from the admin workspace.',
        'visualFootPrimary' => 'Admin portal',
        'visualFootSecondary' => 'Operations management'
    ])
@endsection
