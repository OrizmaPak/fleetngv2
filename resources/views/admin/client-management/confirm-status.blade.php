@extends('layouts/contentLayoutMaster')
@section('title', 'Confirm Client Status')
@section('content')
<x-fleetng.page-heading>Confirm client status</x-fleetng.page-heading>
<section>
    <p>{{ $client->first_name }} {{ $client->last_name }} will be {{ $client->is_active ? 'deactivated' : 'activated' }}.</p>
    <form method="POST" action="{{ route('client-status-change', $client->id) }}">
        @csrf
        <input type="hidden" name="status" value="{{ $client->is_active ? 0 : 1 }}">
        <div class="fleetng-page-actions">
            <a class="btn btn-outline-secondary" href="{{ route('client-list') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Confirm status change</button>
        </div>
    </form>
</section>
@endsection
