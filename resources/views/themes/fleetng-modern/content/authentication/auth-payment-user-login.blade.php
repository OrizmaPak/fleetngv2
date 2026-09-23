@extends('layouts/fullLayoutMaster')
@section('title', 'Payment User Login')
@section('content')
    @include('themes.fleetng-modern.content.authentication._login', ['portalTitle' => 'Payment User Login', 'portalDescription' => 'Access trip and payment operations.', 'action' => route('auth-user-login')])
@endsection
