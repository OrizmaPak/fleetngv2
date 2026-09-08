@extends('layouts/contentLayoutMaster')

@section('title', 'Report ')

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
        <div class="row">
            <div class="col-12">
                <div class="card border-bottom">
                    <p class="text-center text-danger mt-2 mb-0" id="inputValidation" style="display: none"></p>
                    <div class="card-header gap-1">
                        <h4 class="card-title">Get Report</h4>
                        <input type="text" id="TruckID" placeholder="Truck ID" class="form-control"
                            onclick="$('#inputValidation').hide()" style="width: 150px;">
                        <input type="text" id="customer" placeholder="Customer Phone Number" class="form-control"
                            onclick="$('#inputValidation').hide()" style="width: 150px;">
                        <input type="text" id="date_range" class="form-control flatpickr-range"
                            onclick="$('#inputValidation').hide()" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                            style="width: 250px;">
                        <select class="form-control" id="trip_type" style="width: 175px;">
                            <option value="all">All Trips</option>
                            <option value="1">New Trips</option>
                            <option value="2">Live Trips</option>
                            <option value="3">Completed Trips</option>
                            <option value="4">Canceled Trips</option>
                            <option value="5">Declined Trips</option>
                        </select>

                        <select class="form-control" id="payment_type" style="width: 200px;">
                            <option value="">All Payment Status</option>
                            <option>Bank Transfer</option>
                            <option>Paid</option>
                            <option>Pending</option>
                            <option>POS Payment</option>
                            <option>Failed</option>
                        </select>
                    </div>
                    <div class="px-2 pb-2">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <p class="fs-14"><i data-feather="info" data-ticon="info"></i> Maximum limit for downloading PDF is one month (30 days).</p>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-right">
                                    <button class="btn btn-success" onclick="newReport('view')">View</button>
                                    <button class="btn btn-primary" onclick="newReport('download')" id="downloadReportBtn">Download</button>
                                </div>
                            </div>
                        </div>                      
                    </div>
                </div>

            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h4 class="text-center m-2 " style="display: none" id="serverMessage"></h4>
                    <div class="" style="display: none" id="reportSection">
                        <div class="row">
                            <div class="col">
                                <div class="m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Completed Trip</h6>
                                        <h2 class="font-weight-bolder mb-1" id="completed_trips"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Live Trip</h6>
                                        <h2 class="font-weight-bolder mb-1" id="live_trips"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>New Trip</h6>
                                        <h2 class="font-weight-bolder mb-1" id="new_trips">0</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Cancelled Trip</h6>
                                        <h2 class="font-weight-bolder mb-1" id="cancel_trips"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Paid Trips</h6>
                                        <h2 class="font-weight-bolder mb-1" id="paid_trips"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-3 col-12">
                                <div class=" m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Total Earning</h6>
                                        <h2 class="font-weight-bolder mb-1" id="total_earning"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class=" m-1 border shadow ">
                                    <div class="card-body pb-50">
                                        <h6>Profit <b id="profit_percentage"></b></h6>
                                        <h2 class="font-weight-bolder mb-1" id="profit"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class=" m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Total Commision</h6>
                                        <h2 class="font-weight-bolder mb-1" id="total_commission"></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class=" m-1 border shadow">
                                    <div class="card-body pb-50">
                                        <h6>Client Due Payments</h6>
                                        <h2 class="font-weight-bolder mb-1" id="trip_due_amount"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-2 border p-1 datatable">
                            <h6>Trip History</h6>
                            <div class="table table-responsive">
                                <table class="datatables-ajax table" id="userList">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Driver</th>
                                            <th>Client</th>
                                            <th>Pickup</th>
                                            <th>Drop off</th>
                                            <th>Total Cost(in NGN)</th>
                                            <th>Trip Cost(in NGN)</th>
                                            <th>Cost of sand(in NGN)</th>
                                            <th>Road money(in NGN)</th>
                                            <th>Commission(in NGN)</th>
                                            <th>status</th>
                                            <th>Payment Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tripDetail">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

    {{-- date range picker --}}
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>

@endsection

@section('page-script')
    {{-- Page js files --}}
    <script type="text/javascript">
        $(window).on('load', function() {
          if (feather) {
            feather.replace({
              width: 14
              , height: 14
            });
          }
        });

        $('#date_range').on('change', (e) => {
            let daterange = e.target.value.split("to");
            let startDate = daterange[0] || "";
            let endDate = daterange[1] || "";
            let days = 0;
            if (startDate && endDate) {
                let difference = new Date(endDate).getTime() - new Date(startDate).getTime();
                days = Math.floor(difference / (1000 * 60 * 60 * 24)) + 1;
            }

            let btn = $('#downloadReportBtn');

            if(days > 31){
                btn.attr('disabled', true);
            }else{
                btn.attr('disabled', false);
            }
        })

        function newReport(action) {
            var truck_id = $('#TruckID').val();
            var daterange = $('#date_range').val();
            var customer = $('#customer').val();
            if ((truck_id == '' && customer == '' && daterange == '')) {
                $('#inputValidation').html('Please fill in one of the input fields!');
                $('#inputValidation').show();
                return false
            } else {
                if (action == 'view') {
                    getReport();
                }
                if (action == 'download') {
                    getReportPDF();
                }
            }
        }

        function getReport() {
            var truck_id = $('#TruckID').val();
            var daterange = $('#date_range').val();
            var trip_type = $('#trip_type').val();
            var payment_type = $('#payment_type').val();
            var customer = $('#customer').val();
            $.ajax({
                type: 'get',
                data: {
                    truck_id: truck_id,
                    daterange: daterange,
                    trip_type: trip_type,
                    payment_type:payment_type,
                    customer: customer
                },
                url: "{{ url(Route::current()->uri) }}",
                success: function(response) {
                    console.log(response.data);
                    if (response.data !== null) {
                        $('#serverMessage').hide();
                        $('#serverMessage').html('');
                        $('#cancel_trips').html(response.data.cancel_trips);
                        $('#new_trips').html(response.data.new_trips);
                        $('#completed_trips').html(response.data.completed_trips);
                        $('#live_trips').html(response.data.live_trips);
                        $('#paid_trips').html(response.data.paid_trips);
                        $('#trip_due_amount').html(response.data.trip_due_amount.toFixed(2).replace(
                            /\d(?=(\d{3})+\.)/g, '$&,'));
                        $('#total_commission').html(response.data.total_commission.toFixed(2).replace(
                            /\d(?=(\d{3})+\.)/g, '$&,'));
                        $('#total_earning').html(response.data.total_earning.toFixed(2).replace(
                            /\d(?=(\d{3})+\.)/g, '$&,'));
                        $('#reportSection').show();
                        var profit_calculate = ((response.data.total_earning - response.data.total_commission) *
                            100) / response.data.total_earning;
                        var profit_calculate = profit_calculate || 0;
                        var profit_percentage = '(' + Number(profit_calculate).toFixed(2) + '%)';
                        $('#profit_percentage').html(profit_percentage);
                        $('#profit').html(Number(response.data.profit).toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                            '$&,'));
                        var trip = response.data.trips_detail;
                        var status_type = {
                            1: "New",
                            2: "Live",
                            3: 'Complete',
                            4: "Canceled",
                            5: "Declined"
                        };
                        $('#tripDetail').html('');
                        for (var i = 0; i < trip.length; i++) {
                            var drop_location = trip[i].drop_location;
                            var first_name = trip[i].first_name;
                            var last_name = trip[i].last_name;
                            var client_name = trip[i].client_name;
                            var pickup_location = trip[i].location_name;
                            if (trip[i].driver_commission != null) {
                                var driver_commission = trip[i].driver_commission.toFixed(2).replace(
                                    /\d(?=(\d{3})+\.)/g, '$&,');
                            } else {
                                var driver_commission = '';
                            }
                            var total_trip_cost = trip[i].total_trip_cost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            var total_cost = trip[i].total_cost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            var cost_of_sand = (trip[i].cost_of_sand || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            var road_money = (trip[i].road_money || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            var trip_generated_at = new Date(trip[i].trip_generated_at).toLocaleDateString(
                                'en-GB');
                            var status = status_type[trip[i].status];
                            var payment_status = trip[i].payment_status;

                            if (payment_status === 'Pending') {
                                payment_status = '<span class="badge badge-warning">Pending</span>';
                            }

                            if (payment_status === 'Bank Transfer') {
                                payment_status = '<span class="badge badge-success">Bank Transfer</span>';
                            }

                            if (payment_status === 'POS Payment') {
                                payment_status = '<span class="badge badge-success">POS Payment</span>';
                            }

                            if (payment_status === 'Paid') {
                                payment_status = '<span class="badge badge-success">Paid</span>';
                            }

                            if (payment_status === 'Failed') {
                                payment_status = '<span class="badge badge-danger">Failed</span>';
                            }

                            var tr = '<tr><td>' + Number(i + 1) + '</td><td>' + trip_generated_at +
                                '</td><td>' + first_name + ' ' + last_name + '</td><td>' + client_name +
                                '</td><td>' + pickup_location + '</td>' +
                                '<td>' + drop_location + '</td><td>' + total_trip_cost + '</td><td>' + total_cost + '</td><td>' + cost_of_sand + '</td><td>' + road_money + '</td><td>' +
                                driver_commission + '</td><td>' + status + '</td><td>' + payment_status +
                                '</td></tr>';
                            $('#tripDetail').append(tr);

                        }
                    } else {
                        $('#reportSection').hide();
                        $('#serverMessage').html(response.message);
                        $('#serverMessage').show();

                    }
                }
            }).fail(function() {
                alert('request fail');
            })
        }

        function getReportPDF() {
            var truck_id = $('#TruckID').val();
            var daterange = $('#date_range').val();
            var trip_type = $('#trip_type').val();
            var customer = $('#customer').val();
            var payment_type = $('#payment_type').val();

            var url = "{{ route('report_pdf_download') }}?truck_id=" + truck_id + '&daterange=' + daterange +
                '&trip_type=' + trip_type + '&customer=' + customer + '&payment_type=' + payment_type;
            window.location.href = url;
        }
    </script>
@endsection
