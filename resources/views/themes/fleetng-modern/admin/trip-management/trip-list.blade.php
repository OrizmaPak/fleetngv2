@extends('layouts/contentLayoutMaster')

@section('title', 'Trip Management')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
<style>
    .dataTables_length,
    .dataTables_info {
        margin-left: 20px;
    }

    #userList_paginate {
        margin-right: 20px;
    }
</style>


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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="">

                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-2">Trip List</h4>
                            <input type="text" id="date_range" class="form-control flatpickr-range mb-2"
                                onchange="newTrip();" placeholder="YYYY-MM-DD to YYYY-MM-DD" style="width: 200px;">
                            <input type="text" id="search_driver" placeholder="Search Driver" onkeyup="newTrip();"
                                class="form-control mb-2" style="width: 150px;">
                            <input type="text" id="search_client" placeholder="Search Client" onkeyup="newTrip();"
                                class="form-control mb-2" style="width: 150px;">
                            <input type="text" id="search_location" placeholder="Search Location" onkeyup="newTrip();"
                                class="form-control mb-2" style="width: 150px;">
                            <select class="form-control mb-2" id="trip_type" style="width: 160px;" onchange="newTrip();">
                                <option value="all">All Trips</option>
                                <option value="1">New Trips</option>
                                <option value="2">Live Trips</option>
                                <option value="3">Completed Trips</option>
                                <option value="4">Canceled Trips</option>
                                <option value="5">Declined Trips</option>
                            </select>
                            @if (Auth::user()->is_payment_user)
                                <div class="mb-2">
                                    <a href="{{ route('trip-add') }}"
                                        class="btn btn-success waves-effect waves-float waves-light"><i
                                            data-feather='plus'></i>&nbsp;Add New Trip</a>
                                </div>

                                <div class="mb-2 text-center" style="display: none" id="markAsCashReceived">
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary waves-effect waves-float waves-light"
                                        onclick="showTransferConfirmModal()">Confirmed - Bank Transfer (<span
                                            id="transferAmount"></span>)</a>
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary waves-effect waves-float waves-light"
                                        onclick="showPosConfirmModal()">Confirm POS Payment (<span
                                            id="posAmount"></span>)</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-ajax table table-responsive" id="userList">
                            <thead>
                                <tr>
                                    @if (Auth::user()->is_payment_user)
                                        <th>
                                            <input type="checkbox" id="checkAllTripsRow" onchange="handleCheckAll(this)">
                                        </th>
                                    @endif
                                    <th>#Id</th>
                                    <th>Date</th>
                                    <th>Driver</th>
                                    <th>Client</th>
                                    <th>Pickup</th>
                                    <th>Drop off</th>
                                    <th>Total Cost(in NGN)</th>
                                    <th>Commission(in NGN)</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--/ Ajax Sourced Server-side -->
    <!-- Modal -->
    <div class="modal fade text-left" id="deleteSliderConfirm" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Cancel Trip?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('trip-cancel') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <p>Are you sure you want to cancel this trip?</p>
                        <input type="hidden" name="trip_id" class="trip_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="sendSMSAlertConfirm" tabindex="-1" role="dialog"
        aria-labelledby="sendSMSAlertConfirmLable" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Send Alert?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('send-sms-payment-alert-to-client') }}" method="post"
                    id="trip-payment-sms-send">
                    @csrf
                    <div class="modal-body text-center">
                        <h4 class="mt-2">Are you sure you want to send payment alert to this client?</h4>
                        <p class="py-2"><b>Client Info</b> <br> <span id="clientName"></span><br><span
                                id="clientPhone"></span></p>
                        <input type="hidden" name="trip_id" id="sms_alert_trip_id">
                        <input type="hidden" name="sms_text" id="sms_text">
                        <button type="submit" class="btn btn-primary">Yes, Send</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="deleteSliderConfirm1" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Delete Trip?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('trip-delete') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <p>Are you sure you want to delete this trip?</p>
                        <input type="hidden" name="trip_id" class="trip_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmTransferPayment" tabindex="-1" aria-labelledby="confirmTransferPaymentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h3 class="modal-title">Confirm Payment</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 class="text-center py-2"><b id="confirmTransferPaymentAmount">0 NGN</b></h2>
                    <h4 class="text-center" style="line-height: 1.5;">Are you sure that you have received the payment from
                        the
                        client in bank?</h4>
                </div>
                <form class="modal-footer" method="POST" action="{{ route('bulk_trip_bank_payment_received') }}">
                    @csrf
                    <input type="hidden" value="" name="transfer_trip_ids" id="transfer_trip_ids">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmPosPayment" tabindex="-1" aria-labelledby="confirmPosPaymentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h3 class="modal-title">Confirm Payment</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 class="text-center py-2"><b id="confirmPosPaymentAmount">0 NGN</b></h2>
                    <h4 class="text-center" style="line-height: 1.5;">Are you sure that you have want to confirmed
                        selected payments POS?</h4>
                </div>
                <form class="modal-footer" method="POST" action="{{ route('bulk_trip_pos_payment_received') }}">
                    @csrf
                    <input type="hidden" value="" name="pos_trip_ids" id="pos_trip_ids">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>


    <div class="toast toast-basic link-copy-toast hide position-fixed" role="alert" aria-live="assertive"
        aria-atomic="true" data-delay="5000" style="bottom: 1rem; right: 1rem;">
        <div class="toast-header">
            <strong class="mr-auto">Trip Link Copied!</strong>
            <button type="button" class="ml-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>


    <div class="toast toast-basic alert-sending-toast hide position-fixed" role="alert" aria-live="assertive"
        aria-atomic="true" data-delay="5000" style="bottom: 1rem; right: 1rem;">
        <div class="toast-header">
            <strong class="mr-auto">Alert Sending!</strong>
            <button type="button" class="ml-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>

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
    <script src="{{ asset(mix('js/scripts/pages/app-todo.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script type="text/javascript">
        $('#admin_trip_list').addClass('active');
        let bulk_cash_receive_ids = [];
        let total_cash = 0;
        let isPaymentUser = Number("{{ Auth::user()->is_payment_user }}");
        let columns = [];

        if (isPaymentUser) {
            columns.push({
                "data": "cash_checkbox",
                "orderable": false
            });
        }

        let dataColumns = [...columns,
            {
                "data": "DT_RowIndex"
            },
            {
                "data": "trip_generated_at",
                "orderable": true
            },
            {
                "data": "name",
                "orderable": false
            },
            {
                "data": "client_name",
                "orderable": false
            },
            {
                "data": "pickup",
                "orderable": false
            },
            {
                "data": "drop_off",
                "orderable": false
            },
            {
                "data": "total_cost",
                "orderable": true
            },
            {
                "data": "driver_commission",
                "orderable": true
            },
            {
                "data": "status",
                "orderable": false
            },
            {
                "data": "payment_status",
                "orderable": false
            },
            {
                "data": "action",
                "orderable": false
            },
        ];

        $(document).ready(function() {
            $.fn.dataTableExt.sErrMode = 'throw';
            let table = $('#userList').DataTable({
                "language": {},
                "order": [],
                "iDisplayLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "paging": true,
                "processing": true,
                "bFilter": false,
                "bInfo": true,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": true,
                "ajax": {
                    "url": "{{ route('trip-list-detail') }}"
                },
                "columns": dataColumns,
            });

            table.on('page', function() {
                bulk_cash_receive_ids = [];
                total_cash = 0;
                $('#markAsCashReceived').hide();
            });
        });

        function enableactive($data, $status) {
            var id = $data;
            var status = $status

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('driver-status') }}',
                type: "POST",
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    location.reload();
                }
            });

        }

        function get_delete_id(id) {
            $(".trip_id").val(id);
        }

        function showTransferConfirmModal() {
            $('#confirmTransferPayment').modal('show');
            $('#confirmTransferPaymentAmount').text(`${total_cash} NGN`);
            $('#transfer_trip_ids').val(bulk_cash_receive_ids.join(','));
        }

        function showPosConfirmModal() {
            $('#confirmPosPayment').modal('show');
            $('#confirmPosPaymentAmount').text(`${total_cash} NGN`);
            $('#pos_trip_ids').val(bulk_cash_receive_ids.join(','));
        }



        function newTrip() {
            var trip_type = $('#trip_type').val();
            var search_driver = $('#search_driver').val();
            var search_client = $('#search_client').val();
            var search_location = $('#search_location').val();
            var search_location = $('#search_location').val();
            var daterange = $('#date_range').val();
            total_cash = 0;
            bulk_cash_receive_ids = [];
            $('#markAsCashReceived').hide();


            // var daterange = daterange.split("to");
            var table = $('#userList').DataTable();

            table.destroy();
            table = null;
            $.fn.dataTableExt.sErrMode = 'throw';
            $('#userList').DataTable({
                "language": {},
                "order": [],
                "iDisplayLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "paging": true,
                "processing": true,
                "bFilter": false,
                "bInfo": true,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": true,
                "ajax": {
                    "url": "{{ route('trip-list-detail-filter') }}",
                    data: {
                        "trip_type": trip_type,
                        "search_driver": search_driver,
                        "search_client": search_client,
                        "search_location": search_location,
                        "daterange": daterange
                    }
                },
                "columns": dataColumns,
            });

        }

        function handleBulkCheckbox(element) {
            let tripId = $(element).data('id');
            let tripCost = $(element).data('total_cost');

            if (element.checked) {
                bulk_cash_receive_ids.push(tripId);
                total_cash = total_cash + tripCost;
            } else {
                bulk_cash_receive_ids = bulk_cash_receive_ids.filter(i => i !== tripId);
                total_cash = total_cash - tripCost;
            }

            $('#posAmount').text(`${total_cash} NGN`);
            $('#transferAmount').text(`${total_cash} NGN`);

            if (bulk_cash_receive_ids.length) {
                $('#markAsCashReceived').show();
                document.getElementById('checkAllTripsRow').checked = true;
            } else {
                $('#markAsCashReceived').hide();
                total_cash = 0;
                document.getElementById('checkAllTripsRow').checked = false;
            }
        }

        function uncheckAllTrip() {
            let checkboxes = document.getElementsByClassName('bulk_payment_receive');

            if (checkboxes) {
                for (let index = 0; index < checkboxes.length; index++) {
                    let checkbox = checkboxes[index];
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            }

            $('#markAsCashReceived').hide();
        }

        function checkAllTrip() {
            let checkboxes = document.getElementsByClassName('bulk_payment_receive');
            if (checkboxes) {
                for (let index = 0; index < checkboxes.length; index++) {
                    let checkbox = checkboxes[index];
                    if (checkbox) {
                        checkbox.checked = true;
                        bulk_cash_receive_ids.push($(checkbox).data('id'));
                        total_cash = total_cash + $(checkbox).data('total_cost');
                    }
                }
            }
            if (total_cash) {
                $('#posAmount').text(`${total_cash} NGN`);
                $('#transferAmount').text(`${total_cash} NGN`);
                $('#markAsCashReceived').show();
            }
        }

        function handleCheckAll(element) {
            bulk_cash_receive_ids = [];
            total_cash = 0;

            if (element.checked) {
                checkAllTrip();
            } else {
                uncheckAllTrip()
            }

        }

        function shareTrip(element) {
            let tripLink = `{{ config('app.front_url') }}`;
            var merchantName = "{{ $merchant_name }}";
            var clientName = $(element).data('client-name').trim();
            var clientPhone = $(element).data('client-phone');

            $('#sendSMSAlertConfirm').modal('show');
            $('#clientName').text(clientName);
            $('#clientPhone').text(clientPhone);

            let message =
                `Hello ${clientName}, This is to notify you of your pending payments for the trips with ${merchantName} - please visit our payment portal ${tripLink} to view confirm and make payments using your phone number ${clientPhone} and OTP. Thank you for your patronage.`;

            $('#sms_text').val(message);

            $('#sms_alert_trip_id').val($(element).data('id'));
        }

        $('#trip-payment-sms-send').on('submit', () => {
            $('.alert-sending-toast').toast('show');
            $('#sendSMSAlertConfirm').modal('hide');
        })

        function copyTripLink(element) {
            let tripLink = `{{ config('app.front_url') }}/trip/${$(element).data('id')}/view`;
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(tripLink).select();
            document.execCommand("copy");
            $temp.remove();
            $('.link-copy-toast').toast('show');
        }

        function copyLink(elem) {
            var link = $(elem).data('link');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(link).select();
            document.execCommand("copy");
            $temp.remove();
        }

        function isMobileDevice() {
            let isMobile = window.matchMedia || window.msMatchMedia;
            if (isMobile) {
                let match_mobile = isMobile("(pointer:coarse)");
                return match_mobile.matches;
            }
            return false;
        }
    </script>
@endsection
