@extends('layouts/contentLayoutMaster')

@section('title', 'Fleet Health Overview')

@section('content')
<section id="dashboard-analytics" aria-labelledby="fleet-health-title">
    <div class="fleetng-page-heading">
        <div>
            <p class="fleetng-eyebrow">Dashboard</p>
            <h1 id="fleet-health-title">Fleet Health Overview</h1>
            <p>Track fleet activity and financial performance from live operational data.</p>
        </div>
        @if(Auth::user()->is_payment_user)
            <a class="btn btn-primary" href="{{ route('trip-add') }}"><i data-feather="plus"></i> Add trip</a>
        @endif
    </div>

    <div class="fleetng-kpi-grid">
        <a class="fleetng-kpi" href="{{ route('trip-list') }}">
            <span class="fleetng-kpi-icon bg-light-primary"><i data-feather="truck"></i></span>
            <span><small>Total trips</small><strong>{{ number_format($total_trip) }}</strong><em>{{ number_format($total_running_trip) }} currently live</em></span>
        </a>
        @unless(Auth::user()->is_payment_user)
            <a class="fleetng-kpi" href="{{ route('driver-list') }}">
                <span class="fleetng-kpi-icon bg-light-info"><i data-feather="users"></i></span>
                <span><small>Drivers</small><strong>{{ number_format($total_drivers) }}</strong><em>{{ number_format($total_active_drivers) }} active</em></span>
            </a>
            <a class="fleetng-kpi" href="{{ route('admin-user-list') }}">
                <span class="fleetng-kpi-icon bg-light-success"><i data-feather="user-check"></i></span>
                <span><small>Users</small><strong>{{ number_format($total_users) }}</strong><em>{{ number_format($total_active_users) }} active</em></span>
            </a>
        @endunless
        <div class="fleetng-kpi">
            <span class="fleetng-kpi-icon bg-light-warning"><i data-feather="trending-up"></i></span>
            <span><small>Total revenue</small><strong>NGN {{ number_format($total_revenue) }}</strong><em>NGN {{ number_format($today_revenue) }} today</em></span>
        </div>
    </div>

    <div class="row match-height mt-2">
        <div class="col-xl-7 col-12">
            <div class="card h-100">
                <div class="card-header d-block">
                    <h2 class="card-title mb-25">Financial Overview</h2>
                    <p class="mb-0 text-muted">Revenue, commission and expense position.</p>
                </div>
                <div class="card-body fleetng-chart-wrap"><canvas id="financial-overview-chart" aria-label="Financial overview chart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-5 col-12">
            <div class="card h-100">
                <div class="card-header d-block">
                    <h2 class="card-title mb-25">Trip Status</h2>
                    <p class="mb-0 text-muted">Current mix across all assignments.</p>
                </div>
                <div class="card-body fleetng-chart-wrap"><canvas id="trip-status-chart" aria-label="Trip status chart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="fleetng-finance-grid mt-2">
        <div><small>Today's revenue</small><strong>NGN {{ number_format($today_revenue) }}</strong></div>
        <div><small>Today's commission</small><strong>NGN {{ number_format($today_commission) }}</strong></div>
        <div><small>Today's expense</small><strong>NGN {{ number_format($today_expenses) }}</strong></div>
        <div><small>Net revenue</small><strong>NGN {{ number_format($net_revenue) }}</strong></div>
        <div><small>Total commission</small><strong>NGN {{ number_format($total_commission) }}</strong></div>
        <div><small>Total expense</small><strong>NGN {{ number_format($total_expenses) }}</strong></div>
    </div>
</section>
@endsection

@section('vendor-script')
<script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
(function () {
    var text = getComputedStyle(document.documentElement).getPropertyValue('--fleet-text-muted').trim() || '#64748b';
    var grid = getComputedStyle(document.documentElement).getPropertyValue('--fleet-border').trim() || 'rgba(15,23,42,.09)';
    Chart.defaults.global.defaultFontColor = text;
    new Chart(document.getElementById('financial-overview-chart'), {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Commission', 'Expenses', 'Net revenue'],
            datasets: [{ data: [{{ (float) $total_revenue }}, {{ (float) $total_commission }}, {{ (float) $total_expenses }}, {{ (float) $net_revenue }}], backgroundColor: ['#377df6', '#0891b2', '#c73b4a', '#168767'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true }, gridLines: { color: grid } }], xAxes: [{ gridLines: { display: false } }] } }
    });
    new Chart(document.getElementById('trip-status-chart'), {
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
