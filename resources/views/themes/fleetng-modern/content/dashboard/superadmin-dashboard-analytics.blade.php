@extends('layouts/contentLayoutMaster')

@section('title', 'Platform Overview')

@section('content')
<section id="dashboard-analytics" aria-labelledby="platform-title">
    <div class="fleetng-page-heading">
        <div>
            <p class="fleetng-eyebrow">Super Admin</p>
            <h1 id="platform-title">Platform Overview</h1>
            <p>Monitor account activity across FleetNG merchants and users.</p>
        </div>
    </div>
    <div class="fleetng-kpi-grid">
        <a class="fleetng-kpi" href="{{ route('user-list') }}"><span class="fleetng-kpi-icon bg-light-primary"><i data-feather="users"></i></span><span><small>Total users</small><strong>{{ number_format($total_users) }}</strong><em>All registered users</em></span></a>
        <a class="fleetng-kpi" href="{{ route('user-list') }}?isActive=1"><span class="fleetng-kpi-icon bg-light-success"><i data-feather="user-check"></i></span><span><small>Active users</small><strong>{{ number_format($total_active_users) }}</strong><em>Currently enabled</em></span></a>
        <a class="fleetng-kpi" href="{{ route('merchant-list') }}"><span class="fleetng-kpi-icon bg-light-info"><i data-feather="briefcase"></i></span><span><small>Merchants</small><strong>{{ number_format($total_merchants) }}</strong><em>Fleet operators</em></span></a>
        <a class="fleetng-kpi" href="{{ route('merchant-list') }}?isActive=1"><span class="fleetng-kpi-icon bg-light-warning"><i data-feather="activity"></i></span><span><small>Active merchants</small><strong>{{ number_format($total_active_merchants) }}</strong><em>{{ number_format($total_inactive_merchants) }} inactive</em></span></a>
    </div>
    <div class="card mt-2">
        <div class="card-header"><h2 class="card-title mb-0">Account Status</h2></div>
        <div class="card-body">
            <div class="fleetng-status-summary">
                <div><span class="fleetng-status fleetng-status-active">Active users</span><strong>{{ number_format($total_active_users) }}</strong></div>
                <div><span class="fleetng-status fleetng-status-inactive">Inactive users</span><strong>{{ number_format($total_inactive_users) }}</strong></div>
                <div><span class="fleetng-status fleetng-status-active">Active merchants</span><strong>{{ number_format($total_active_merchants) }}</strong></div>
                <div><span class="fleetng-status fleetng-status-inactive">Inactive merchants</span><strong>{{ number_format($total_inactive_merchants) }}</strong></div>
            </div>
        </div>
    </div>
</section>
@endsection
