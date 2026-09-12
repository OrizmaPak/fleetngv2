@extends('layouts/contentLayoutMaster')

@section('title', 'Trip Detail')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection


@section('content')
<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>
<x-fleetng.feedback />

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Trip Detail</h4>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-dark mr-1"><i
                                data-feather='chevron-left'></i> Back</a>
                    </div>
                    <br>
                    <div class="card-body">
                        <div class="form form-horizontal" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-12">

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Driver Name</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <a href="{{ route('view-driver', $trip_details['driver']['id']) }}">{{ $trip_details['driver']['first_name'] }}
                                                    {{ $trip_details['driver']['last_name'] }}</a>
                                            </div>
                                            @if ($trip_details['status'] == 5)
                                                <div class="col-sm-3">
                                                    <button class="btn btn-primary" id="driverChangeBtn">Change
                                                        Driver</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($drivers)
                                        <form class="col-12 d-none" action="{{ route('update-trip-driver') }}"
                                            method="POST" id="driverChangeForm">
                                            @csrf
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Choose New Driver</label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <select name="driver_id" id="" class="form-control">
                                                        @foreach ($drivers as $driver)
                                                            <option value="{{ $driver->id }}"
                                                                {{ $trip_details['driver']['id'] === $driver->id ? 'selected' : '' }}>
                                                                {{ $driver->first_name . ' ' . $driver->last_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="hidden" name="trip_id" value="{{ $trip_details['id'] }}">
                                                    <button type="submit" class="btn btn-primary"
                                                        onclick="">Change</button>
                                                    <button type="button" class="btn btn-warning" onclick=""
                                                        id="driverChangeCancelBtn">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Client Name</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ $trip_details['client_name'] }}' readonly="" />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Date & Time</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ date('d-M-Y h:i A', strtotime($trip_details['trip_generated_at'] ?? $trip_details['created_at'])) }}'
                                                    readonly="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Total Trip Cost(in NGN)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ $trip_details['total_trip_cost'] }}' readonly="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Trip Cost(in NGN)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ $trip_details['total_cost'] }}' readonly="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Cost of Sand(in NGN)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ $trip_details['cost_of_sand'] ?? '--' }}' readonly="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Road Money(in NGN)</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ $trip_details['road_money'] ?? '--' }}' readonly="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Pickup Location</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ !empty($trip_details['pickup_location']['location_name']) ? $trip_details['pickup_location']['location_name'] : '--' }}'
                                                    readonly="" />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Drop Location</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    value='{{ !empty($trip_details['drop_location']['location']) ? $trip_details['drop_location']['location'] : '--' }}'
                                                    readonly="" />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Clock-in Time</label>
                                            </div>
                                            <div class="col-sm-9">
                                                @if ($trip_details['clock_in_time'])
                                                    <input type="text" class="form-control"
                                                        value='{{ date('d-M-Y h:i A', strtotime($trip_details['clock_in_time'])) }}'
                                                        readonly="" />
                                                @else
                                                    <input type="text" class="form-control" value='--'
                                                        readonly="" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Clock-out Time</label>
                                            </div>
                                            <div class="col-sm-9">
                                                @if ($trip_details['clock_out_time'])
                                                    <input type="text" class="form-control"
                                                        value='{{ date('d-M-Y h:i A', strtotime($trip_details['clock_out_time'])) }}'
                                                        readonly="" />
                                                @else
                                                    <input type="text" class="form-control" value='--'
                                                        readonly="" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Driver Commission</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control"
                                                    placeholder="Driver Commission"
                                                    value='{{ $trip_details['driver_commission'] ? $trip_details['driver_commission'] : '--' }}'
                                                    readonly="" />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Trip Status</label>
                                            </div>
                                            <div class="col-sm-9">

                                                @if ($trip_details['status'] == 1)
                                                    <input type="text" class="form-control" value='New Trip'
                                                        readonly="" />
                                                @elseif($trip_details['status'] == 2)
                                                    <input type="text" class="form-control" value='Live Trip'
                                                        readonly="" />
                                                @elseif($trip_details['status'] == 3)
                                                    <input type="text" class="form-control" value='Completed Trip'
                                                        readonly="" />
                                                @elseif($trip_details['status'] == 4)
                                                    <input type="text" class="form-control" value='Canceled Trip'
                                                        readonly="" />
                                                @elseif($trip_details['status'] == 5)
                                                    <input type="text" class="form-control" value='Declined Trip'
                                                        readonly="" />
                                                @else
                                                    <input type="text" class="form-control" value='--'
                                                        readonly="" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Payment Status</label>
                                            </div>
                                            <div class="col-sm-9">
                                                @if ($trip_details['payment_confirm_by_bank_transfer'])
                                                    <input type="text" class="form-control" value="Bank Transfer"
                                                        readonly="" />
                                                @elseif($trip_details['payment_confirmed_by_pos'])
                                                    <input type="text" class="form-control" value="POS Payment"
                                                        readonly="" />
                                                @else
                                                    <input type="text" class="form-control"
                                                        value="{{ $trip_details['payment_status'] ?? '--' }}" readonly="" />
                                                @endif

                                            </div>
                                        </div>

                                        @if ($trip_details['payment'])
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Payment Transaction ID</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class=""> <input type="text" class="form-control"
                                                            value="{{ $trip_details['payment']['transaction_id'] ?? '--' }}"
                                                            readonly="" /></div>

                                                    @if (Auth::user()->is_payment_user)
                                                        <p><a
                                                                href="{{ route('report_invoice_pdf_download', $trip_details['id']) }}">
                                                                <u>Download
                                                                    Invoice</u>
                                                            </a></p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if (Auth::user()->is_payment_user && $trip_details['status'] == 3 && !$trip_details['payment'])
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">&nbsp;</label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <button data-target="#confirmPayment" data-toggle="modal"
                                                        class="btn btn-success waves-effect waves-float waves-light">Confirmed
                                                        - Bank Transfer</button>

                                                    <button data-target="#confirmPaymentPOS" data-toggle="modal"
                                                        class="btn btn-success waves-effect waves-float waves-light">Confirmed
                                                        POS</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="modal fade" id="confirmPayment" tabindex="-1" aria-labelledby="confirmPaymentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h3 class="modal-title" id="confirmPaymentLabel">Confirmed - Bank Transfer</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="py-2 text-center" style="line-height: 1.5;">Are you sure that you have received the payment
                        from the
                        client in bank?</h4>
                </div>
                <form class="modal-footer" method="POST"
                    action="{{ route('trip_bank_payment_received', $trip_details['id']) }}">
                    @csrf
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmPaymentPOS" tabindex="-1" aria-labelledby="confirmPaymentPOSLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h3 class="modal-title" id="confirmPaymentPOSLabel">Confirmed POS</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="py-2 text-center" style="line-height: 1.5;">Are you sure that you have want to confirmed
                        payment POS?</h4>
                </div>
                <form class="modal-footer" method="POST" action="{{ route('trip_confirm_pos', $trip_details['id']) }}">
                    @csrf
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>


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

@section('page-script')

    <script type="text/javascript">
        $('#driverChangeBtn').on('click', () => {
            $('#driverChangeBtn').addClass('d-none');
            $('#driverChangeForm').removeClass('d-none');
        })

        $('#driverChangeCancelBtn').on('click', () => {
            $('#driverChangeBtn').removeClass('d-none');
            $('#driverChangeForm').addClass('d-none');
        })
    </script>
    {{-- Page js files --}}
    <script type="text/javascript"></script>
@endsection
