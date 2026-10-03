@extends('layouts/fullLayoutMaster')
@section('title', 'User Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', [
        'portalTitle' => 'User Login',
        'portalDescription' => 'Access your FleetNG operations workspace.',
        'action' => route('auth-user-login'),
        'loginImage' => 'themes/fleetng-modern/images/login/operations-command.png',
        'loginImageAlt' => 'FleetNG operations command center',
        'visualKicker' => 'Operations workspace',
        'visualTitle' => 'Coordinate trips, drivers, and customers from one place.',
        'visualDescription' => 'Use this portal for daily fleet operations, trip management, tracking, and dispatch workflows.',
        'visualFootPrimary' => 'User portal',
        'visualFootSecondary' => 'Fleet operations access'
    ])
@endsection
