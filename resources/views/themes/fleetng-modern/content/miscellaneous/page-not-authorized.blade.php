@extends('layouts/fullLayoutMaster')
@section('title', 'Not Authorized')
@section('content')
@include('content.miscellaneous._state', ['icon' => 'shield', 'eyebrow' => 'Access restricted', 'heading' => 'You cannot access this page', 'message' => 'Your account does not have permission for this operation.'])
@endsection
