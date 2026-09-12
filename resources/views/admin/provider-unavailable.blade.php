@extends('layouts/contentLayoutMaster')
@section('title', 'Tracking')
@section('content')
<x-fleetng.page-heading>Tracking</x-fleetng.page-heading>
<section class="fleetng-empty-state" role="status">
    <h2>Tracking is not connected</h2>
    <p>No live position or route history is available. Bookings and driver records remain available.</p>
    <a class="btn btn-primary" href="{{ route('driver-list') }}">Driver list</a>
</section>
@endsection
