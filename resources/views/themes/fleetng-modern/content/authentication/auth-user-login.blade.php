@extends('layouts/fullLayoutMaster')
@section('title', 'Company User Login')
@section('content')
    @include('content.authentication._login', ['portalTitle' => 'Company User Login', 'portalDescription' => 'Access your assigned fleet workspace.', 'action' => route('auth-user-login')])
@endsection
