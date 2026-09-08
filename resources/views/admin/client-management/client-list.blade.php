@extends('layouts/contentLayoutMaster')
@if (Auth::user()->user_type == 1)
    @section('title', 'Client Management')
@else
    @section('title', 'Clients')
@endif

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    <style>
        .dataTables_length,
        .dataTables_info {
            margin-left: 20px;
        }

        #userList_paginate,
        #userList_filter {
            margin-right: 20px;
        }
    </style>
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
        <button class="btn btn-outline-primary toast-basic-toggler mt-2" style="display:none;" id="click_me">Toast</button>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Clients</h4>
                    </div>
                    <div class="card-datatable table table-responsive">
                        <table class="datatables-ajax table" id="userList">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fullname</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal -->
    <div class="modal fade text-left" id="deleteSliderConfirm" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Delete Slider?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('client-delete') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <p>Are you sure you want to delete this client?</p>
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <input type="hidden" name="client_id" id="client_id">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Basic toast -->

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
    <script>
        function setActionId(id) {
            $("#client_id").val(id);
        }
        $("#merchant_client_list").addClass('active'); // show cuurent sidebar tab active;

        $(document).ready(function() {
            $.fn.dataTableExt.sErrMode = 'throw';
            let table = $('#userList').DataTable({
                language: {
                    searchPlaceholder: 'Search...'
                },
                "searchable": true,
                "searchDelay": 350,
                "order": [],
                "iDisplayLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                "paging": true,
                "processing": true,
                "bFilter": true,
                "bInfo": true,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": true,
                "ajax": {
                    "url": "{{ route('client-list-detail') }}"
                },
                "columns": [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "full_name",
                        searchable: true,
                        "orderable": true
                    },
                    {
                        "data": "phone_number",
                        searchable: true,
                        "orderable": false
                    },
                    {
                        "data": "email",
                        "orderable": false
                    },
                    {
                        "data": "action",
                        "orderable": false
                    },
                ],
            });

        });
    </script>
@endsection
