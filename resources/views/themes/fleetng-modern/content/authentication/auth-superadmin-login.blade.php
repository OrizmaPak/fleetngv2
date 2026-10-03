@extends('layouts/fullLayoutMaster')
@section('title', 'Super Admin Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', [
        'portalTitle' => 'Super Admin Login',
        'portalDescription' => 'Sign in to administer the FleetNG platform.',
        'action' => route('auth-superadmin-login'),
        'loginImage' => 'themes/fleetng-modern/images/login/platform-control.png',
        'loginImageAlt' => 'FleetNG platform control room',
        'visualKicker' => 'Platform oversight',
        'visualTitle' => 'Control the FleetNG network from the highest access layer.',
        'visualDescription' => 'Use this portal for platform administration, merchant oversight, permissions, and system-wide configuration.',
        'visualFootPrimary' => 'Super admin',
        'visualFootSecondary' => 'Platform governance'
    ])
@endsection
