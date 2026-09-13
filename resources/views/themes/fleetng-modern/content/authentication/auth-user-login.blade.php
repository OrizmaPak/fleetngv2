@extends('layouts/fullLayoutMaster')
@section('title', 'User Login')
@section('content')
    @include('content.authentication._login', ['portalTitle' => 'User Login', 'portalDescription' => 'Access your FleetNG operations workspace.', 'action' => route('auth-user-login')])
@endsection
