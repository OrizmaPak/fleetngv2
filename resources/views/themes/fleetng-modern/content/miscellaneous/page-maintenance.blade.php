@extends('layouts/fullLayoutMaster')
@section('title', 'Maintenance')
@section('content')
@include('content.miscellaneous._state', ['icon' => 'tool', 'eyebrow' => 'Scheduled maintenance', 'heading' => 'FleetNG will be back shortly', 'message' => 'The operations portal is temporarily unavailable while maintenance is completed.'])
@endsection
