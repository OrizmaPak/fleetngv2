@php
    $pageConfigs = ['pageHeader' => false];
@endphp
@extends('layouts/contentLayoutMaster')

@section('title', Auth::user()->user_type == 1 ? 'Client Management' : 'Clients')

@section('content')
<section aria-labelledby="clients-title">
    <div class="fleetng-page-heading">
        <div>
            <p class="fleetng-eyebrow">Directory</p>
            <h1 id="clients-title">Clients</h1>
            <p>Find and manage the customers connected to your fleet operations.</p>
        </div>
    </div>

    @if(Session::get('success'))
        <div class="alert alert-success" role="status">{{ Session::get('success') }}</div>
    @endif
    @if(Session::get('fail'))
        <div class="alert alert-danger" role="alert">{{ Session::get('fail') }}</div>
    @endif

    <div class="card">
        <x-fleetng.data-table
            id="clients-table"
            :endpoint="route('client-list-detail')"
            label="Clients"
            :columns="[
                ['key' => 'full_name', 'label' => 'Full name', 'sortable' => true, 'sort' => 'first_name'],
                ['key' => 'phone_number', 'label' => 'Phone number'],
                ['key' => 'email', 'label' => 'Email', 'sortable' => true],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status', 'sortable' => true, 'sort' => 'is_active'],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions'],
            ]"
        />
    </div>
</section>

<div class="modal fade" id="deleteClientConfirm" tabindex="-1" role="dialog" aria-labelledby="delete-client-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h4" id="delete-client-title">Delete client?</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('client-delete') }}" method="post">
                @csrf
                <div class="modal-body"><p>This client will be removed from the directory. This action cannot be undone.</p></div>
                <div class="modal-footer">
                    <input type="hidden" name="client_id" id="modern_client_id">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete client</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
