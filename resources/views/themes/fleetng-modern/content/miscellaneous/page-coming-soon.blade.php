@extends('layouts/fullLayoutMaster')
@section('title', 'Coming Soon')
@section('content')
@include('content.miscellaneous._state', ['icon' => 'clock', 'eyebrow' => 'Coming soon', 'heading' => 'This workspace is being prepared', 'message' => 'The requested FleetNG feature is not available yet.'])
@endsection
