@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('content')
<section id="dashboard-analytics" aria-labelledby="fleet-health-title">
    <div class="fleetng-page-heading">
        <div>
            <p class="fleetng-eyebrow">Dashboard</p>
            <h1 id="fleet-health-title">Dashboard Analytics</h1>
            <p>Welcome {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</p>
        </div>
        @if(Auth::user()->is_payment_user)
            <a class="btn btn-primary" href="{{ route('trip-add') }}"><i data-feather="plus"></i> Add trip</a>
        @endif
    </div>

    @if (!empty($merchant['merchant_name']))
        <p class="text-right"><span>Merchant Name:</span> {{ $merchant['merchant_name'] }}</p>
    @endif

    <div class="card card-statistics">
        <div class="card-header">
            <h2 class="card-title mb-0">Total Trips: {{ number_format($total_trip) }}</h2>
        </div>
    </div>

    <h2 class="fleetng-section-title mt-2">Live Fleet Status</h2>
    <div class="fleetng-kpi-grid">
        @unless(Auth::user()->is_payment_user)
            <a class="fleetng-kpi" href="{{ route('driver-list') }}">
                <span class="fleetng-kpi-icon bg-light-info"><i data-feather="users"></i></span>
                <span><small>Total Drivers</small><strong>{{ number_format($total_drivers) }}</strong><em>All assigned drivers</em></span>
            </a>
            <a class="fleetng-kpi" href="{{ route('driver-list', ['isActive' => 1]) }}">
                <span class="fleetng-kpi-icon bg-light-success"><i data-feather="user-check"></i></span>
                <span><small>Total Active Drivers</small><strong>{{ number_format($total_active_drivers) }}</strong><em>Currently active</em></span>
            </a>
        @endunless
        <a class="fleetng-kpi" href="{{ route('trip-list') }}">
            <span class="fleetng-kpi-icon bg-light-primary"><i data-feather="truck"></i></span>
            <span><small>Total Running Trips</small><strong>{{ number_format($total_running_trip) }}</strong><em>{{ number_format($total_trip) }} total trips</em></span>
        </a>
        @unless(Auth::user()->is_payment_user)
            <a class="fleetng-kpi" href="{{ route('driver-list', ['isActive' => 0]) }}">
                <span class="fleetng-kpi-icon bg-light-danger"><i data-feather="user-x"></i></span>
                <span><small>Total Inactive Drivers</small><strong>{{ number_format($total_inactive_drivers) }}</strong><em>Currently inactive</em></span>
            </a>
        @endunless
    </div>

    @if (!Auth::user()->is_payment_user)
    <h2 class="fleetng-section-title mt-2">Financials</h2>
    <div class="fleetng-finance-grid mt-1">
        <a href="{{ route('trip-list') }}"><small>Today's Revenue</small><strong>{{ number_format($today_revenue) }}</strong></a>
        <a href="{{ route('trip-list') }}"><small>Today's Commission</small><strong>{{ number_format($today_commission) }}</strong></a>
        <a href="{{ route('expenses') }}"><small>Today's Expense</small><strong>{{ number_format($today_expenses) }}</strong></a>
        <a href="{{ route('trip-list') }}"><small>Total Revenue</small><strong>{{ number_format($total_revenue) }}</strong></a>
        <a href="{{ route('expenses') }}"><small>Overall Expenses</small><strong>{{ number_format($total_expenses) }}</strong></a>
        <a href="{{ route('trip-list') }}"><small>Overall Commission</small><strong>{{ number_format($total_commission) }}</strong></a>
    </div>

    <div class="row match-height mt-2">
        <div class="col-xl-7 col-12">
            <div class="card h-100">
                <div class="card-header d-block">
                    <h2 class="card-title mb-25">Revenue/Expense Ratio</h2>
                    <p class="mb-0 text-muted">Revenue, commission and expense position.</p>
                </div>
                <div class="card-body fleetng-chart-wrap"><canvas id="financial-overview-chart" aria-label="Financial overview chart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-5 col-12">
            <div class="card h-100">
                <div class="card-header d-block">
                    <h2 class="card-title mb-25">Trip Status</h2>
                    <p class="mb-0 text-muted">Current trip mix across all assignments.</p>
                </div>
                <div class="card-body fleetng-chart-wrap"><canvas id="trip-status-chart" aria-label="Trip status chart"></canvas></div>
            </div>
        </div>
    </div>

    <h2 class="fleetng-section-title mt-2">Savings Analytics</h2>
    <div class="fleetng-finance-grid fleetng-savings-grid mt-1">
        <div><small>Overall Savings</small><strong>{{ number_format($overall_savings) }}</strong><em>Prev Week: {{ number_format($previous_week_overall_savings) }}</em></div>
        <div><small>Overall Savings Withdrawals</small><strong>{{ number_format($overall_savings_withdrawals) }}</strong><em>Prev Week: {{ number_format($previous_week_overall_savings_withdrawals) }}</em></div>
        <div><small>Total Driver Savings</small><strong>{{ number_format($total_driver_savings) }}</strong><em>Prev Week: {{ number_format($previous_week_driver_savings) }}</em></div>
        <div><small>Total Motorboy Savings</small><strong>{{ number_format($total_motorboy_savings) }}</strong><em>Prev Week: {{ number_format($previous_week_motorboy_savings) }}</em></div>
        <div><small>Total Driver Withdrawals</small><strong>{{ number_format($total_driver_withdrawals) }}</strong><em>Prev Week: {{ number_format($previous_week_driver_withdrawals) }}</em></div>
        <div><small>Total Motorboy Withdrawals</small><strong>{{ number_format($total_motorboy_withdrawals) }}</strong><em>Prev Week: {{ number_format($previous_week_motorboy_withdrawals) }}</em></div>
    </div>
    @endif
</section>
@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
(function () {
    var financialChart = document.getElementById('financial-overview-chart');
    var tripStatusChart = document.getElementById('trip-status-chart');
    if (!financialChart || !tripStatusChart) return;

    var text = getComputedStyle(document.documentElement).getPropertyValue('--fleet-text-muted').trim() || '#64748b';
    var grid = getComputedStyle(document.documentElement).getPropertyValue('--fleet-border').trim() || 'rgba(15,23,42,.09)';
    Chart.defaults.global.defaultFontColor = text;
    new Chart(financialChart, {
        type: 'bar',
        data: {
            labels: ['Total Revenue', 'Total Expense'],
            datasets: [{ data: [{{ (float) $total_revenue }}, {{ (float) $total_expenses }}], backgroundColor: ['#0891b2', '#b46b08'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true }, gridLines: { color: grid } }], xAxes: [{ gridLines: { display: false } }] } }
    });
    new Chart(tripStatusChart, {
        type: 'doughnut',
        data: {
            labels: ['New', 'Live', 'Completed', 'Cancelled', 'Declined'],
            datasets: [{ data: [{{ $total_new_trip }}, {{ $total_running_trip }}, {{ $total_completed_trip }}, {{ $total_canceled_trip }}, {{ $total_declined_trip }}], backgroundColor: ['#377df6', '#0891b2', '#168767', '#c73b4a', '#b46b08'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutoutPercentage: 68, legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 18 } } }
    });
}());
</script>
@endsection
