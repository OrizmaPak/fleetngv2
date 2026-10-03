@extends('layouts/fullLayoutMaster')
@section('title', 'Payment User Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', [
        'portalTitle' => 'Payment User Login',
        'portalDescription' => 'Access trip and payment operations.',
        'action' => route('auth-payment-user-login'),
        'loginImage' => 'themes/fleetng-modern/images/login/payment-operations.png',
        'loginImageAlt' => 'FleetNG payment operations desk',
        'visualKicker' => 'Payment operations',
        'visualTitle' => 'Review trip payments with finance-grade clarity.',
        'visualDescription' => 'Use this portal for payment checks, reconciliation, trip invoices, and customer payment activity.',
        'visualFootPrimary' => 'Payment portal',
        'visualFootSecondary' => 'Trip finance access'
    ])
@endsection
