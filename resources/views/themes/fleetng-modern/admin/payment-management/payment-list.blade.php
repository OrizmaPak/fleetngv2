@extends('layouts/contentLayoutMaster')
@if (Auth::user()->user_type == 1)
    @section('title', 'Payment Management')
@else
    @section('title', 'Payments')
@endif

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection


@section('content')
<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>
<x-fleetng.feedback />
    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @if (Session::get('success'))
                        <div class="demo-spacing-0">
                            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                <div class="alert-body">
                                    {{ Session::get('success') }}
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    @if (Session::get('fail'))
                        <div class="demo-spacing-0">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="alert-body">
                                    {{ Session::get('fail') }}
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    <br>
                </div>
            </div>
        </div>
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" style="display:none;" id="click_me">Toast</button>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Payments</h4>
                    </div>
                    <div class="card-datatable table table-responsive">
                        <table class="datatables-ajax table" id="userList">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Transaction ID/Date</th>
                                    <th>Trip ID/ Date</th>
                                    <th>Phone Number</th>
                                    <th>Pick-up/Drop-off</th>
                                    <th>Amount (NGN)</th>
                                    <th>Payment Mode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <p>#{{ $payment->transaction_id }}</p>
                                        <p>{{ $payment->created_at }}</p>
                                    </td>
                                    <td>
                                        @if ($payment->trip)
                                            <p>#{{ $payment->trip->id }}</p>
                                            <p>{{ $payment->trip->created_at }}</p>
                                        @else
                                            <p>--</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->trip && $payment->trip->client)
                                            <p>{{ $payment->trip->client->phone_number }}</p>
                                        @else
                                            <p>--</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->trip)
                                            <p>{{ $payment->trip->pickup_location }}/{{ $payment->trip->drop_location }}
                                            </p>
                                        @else
                                            <p>--</p>
                                        @endif
                                    </td>
                                    <td>{{ $payment->amount }}</td>
                                    <td>{{ $payment->payment_type }}</td>
                                    <td>
                                        @if ($payment->status === 'successful')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif ($payment->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif ($payment->status === 'failed')
                                            <span class="badge badge-danger">Paid</span>
                                        @else
                                            <span class="">{{$payment->status}}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--/ Ajax Sourced Server-side -->
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection