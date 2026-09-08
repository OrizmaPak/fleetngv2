@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap.min.css')) }}">
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- Dashboard Analytics Start -->
    <p class="text-right">
        @if (!empty($merchant['merchant_name']))
            <span>Merchant Name:</span> {{ $merchant['merchant_name'] }}
    </p>
    @endif
    <section id="dashboard-analytics">
        <div class="row match-height">

            <!-- Statistics Card -->
            <div class="col-xl-12 col-12">

                <h4 class="card-title">Welcome {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</h4>
                <div class="card card-statistics">
                    <div class="card-header">
                        <h4 class="card-title">Total Trips: {{ $total_trip }}</h4>
                        @if (Auth::user()->is_payment_user)
                            <div class="d-flex align-items-center">
                                <a class="btn btn-success" href="{{ route('trip-add') }}">Add New Trip</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="card card-statistics">
                        <div class="card-header">
                            <h4 class="card-title">Live Fleet Status</h4>
                            <div class="d-flex align-items-center">
                                <!-- <p class="card-text font-small-2 mr-25 mb-0">Updated just now</p> -->
                            </div>
                        </div>
                        <div class="card-body statistics-body">
                            @if (Auth::user()->is_payment_user)
                                <div class="row">
                                    <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                        <a href="{{ route('trip-list') }}">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="truck" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0">{{ $total_running_trip }}</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Running Trips</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @else
                                @if (session('user_role') == 1)
                                    <div class="row mb-2">
                                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                            <a href="{{ route('user-list') }}">
                                                <div class="media">
                                                    <div class="avatar bg-light-primary mr-2">
                                                        <div class="avatar-content">
                                                            <i data-feather="user" class="avatar-icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="media-body my-auto">
                                                        <h4 class="font-weight-bolder mb-0">{{ $total_users }}</h4>
                                                        <p class="card-text font-small-3 mb-0">Total Users</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                            <a href="{{ route('user-list') }}">
                                                <div class="media">
                                                    <div class="avatar bg-light-primary mr-2">
                                                        <div class="avatar-content">
                                                            <i data-feather="user" class="avatar-icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="media-body my-auto">
                                                        <h4 class="font-weight-bolder mb-0">{{ $total_active_users }}</h4>
                                                        <p class="card-text font-small-3 mb-0">Total Active Users</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-3 col-sm-6 col-12">
                                            <a href="{{ route('user-list') }}">
                                                <div class="media">
                                                    <div class="avatar bg-light-primary mr-2">
                                                        <div class="avatar-content">
                                                            <i data-feather="user" class="avatar-icon"></i>
                                                        </div>
                                                    </div>
                                                    <div class="media-body my-auto">
                                                        <h4 class="font-weight-bolder mb-0">{{ $total_inactive_users }}
                                                        </h4>
                                                        <p class="card-text font-small-3 mb-0">Total Inactive Users</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                                <div class="row">
                                    <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                        <a href="{{ route('driver-list') }}">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="user" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0">{{ $total_drivers }}</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Drivers</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                        <a href="{{ route('driver-list', ['isActive' => 1]) }}">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="user" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0">{{ $total_active_drivers }}</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Active Drivers</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                                        <a href="{{ route('trip-list') }}">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="truck" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0">{{ $total_running_trip }}</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Running Trips</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-xl-3 col-sm-6 col-12">
                                        <a href="{{ route('driver-list', ['isActive' => 0]) }}">
                                            <div class="media">
                                                <div class="avatar bg-light-primary mr-2">
                                                    <div class="avatar-content">
                                                        <i data-feather="user" class="avatar-icon"></i>
                                                    </div>
                                                </div>
                                                <div class="media-body my-auto">
                                                    <h4 class="font-weight-bolder mb-0">{{ $total_inactive_drivers }}</h4>
                                                    <p class="card-text font-small-3 mb-0">Total Inactive Drivers</p>
                                                </div>
                                            </div>
                                        </a>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if (!Auth::user()->is_payment_user)
                        <div class="row">
                            <div class="col-xl-7 col-12">
                                <div class="card card-statistics">
                                    <div class="card-header">
                                        <h4 class="card-title">Financials</h4>
                                        <div class="d-flex align-items-center">
                                            <!-- <p class="card-text font-small-2 mr-25 mb-0">Updated just now</p> -->
                                        </div>
                                    </div>
                                    <div class="card-body statistics-body">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                                <a href="{{ route('trip-list') }}">

                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-up" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($today_revenue) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Today's Revenue</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                                <a href="{{ route('trip-list') }}">
                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-up" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($today_commission) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Today's Commission</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12">
                                                <a href="{{ route('expenses') }}">
                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-down" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($today_expenses) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Today's Expense</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <hr class="my-3">

                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                                <a href="{{ route('trip-list') }}">
                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-up" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($total_revenue) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Total Revenue</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                                <a href="{{ route('expenses') }}">
                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-down" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($total_expenses) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Overall Expenses</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <div class="col-md-4 col-sm-6 col-12">
                                                <a href="{{ route('trip-list') }}">
                                                    <div class="media">
                                                        <div class="avatar bg-light-primary mr-2">
                                                            <div class="avatar-content">
                                                                <i data-feather="trending-up" class="avatar-icon"></i>
                                                            </div>
                                                        </div>
                                                        <div class="media-body my-auto">
                                                            <h4 class="font-weight-bolder mb-0">
                                                                {{ Helper::numFormat($total_commission) }}
                                                            </h4>
                                                            <p class="card-text font-small-3 mb-0">Overall Commission</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Donut Chart Starts-->
                            <div class="col-xl-5 col-12">
                                <div class="card">
                                    <div class="card-header flex-column align-items-start pb-1">
                                        <h4 class="card-title mb-75">Revenue/Expense Ratio</h4>
                                        {{-- <span class="card-subtitle text-muted">Spending on various categories </span> --}}
                                    </div>
                                    <div class="card-body">
                                        <canvas id="donut-chart1"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- Donut Chart Ends-->
                        </div>
                    @endif
                </div>

            </div>
            <!--/ Statistics Card -->
        </div>



    </section>
    <!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap.min.js')) }}"></script>

    <script>
        $('#admin_analytics').addClass('active');

        let revenue = Number("{{ $total_revenue }}");
        let expense = Number("{{ $total_expenses }}");

        const labels = ['Total Revenue', 'Total Expense'];
        const data = {
            labels: labels,
            datasets: [{
                data: [revenue, expense],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                ],
                borderColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                },
                legend: {
                    display: false,
                    labels: {
                        display: false,
                    }
                }
            },
        };
        var barChart = document.getElementById('donut-chart1');

        new Chart(barChart, config);
    </script>
@endsection
