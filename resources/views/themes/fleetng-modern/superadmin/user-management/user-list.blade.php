@extends('layouts/contentLayoutMaster')

@section('title', 'User Management')

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
                        <h4 class="card-title">User List</h4>
                        <input type="text" id="search_user" placeholder="Search User" onkeyup="newUser();"
                            class="form-control" style="width: 150px;">
                        <input type="text" id="search_email" placeholder="Search Email" onkeyup="newUser();"
                            class="form-control" style="width: 150px;">
                        <input type="text" id="search_phone" placeholder="Search Phone" onkeyup="newUser();"
                            class="form-control" style="width: 150px;">
                        <select class="form-control" id="user_type" style="width: 150px;" onchange="newUser();">
                            <option value="all">All User</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <select class="form-control" id="role" style="width: 150px;" onchange="newUser();">
                            <option value="all">All User Types</option>
                            <option value="1">Normal User</option>
                            <option value="2">Payment User</option>
                        </select>
                        <a href="{{ route('add-user-page') }}"
                            class="btn btn-success waves-effect waves-float waves-light"><i
                                data-feather='plus'></i>&nbsp;Add New user</a>
                    </div>

                    <div class="card-datatable table table-responsive">
                        <table class="datatables-ajax table" id="userList">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>User Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Last Active</th>
                                    <th>Status</th>
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
                    <h4 class="modal-title" id="myModalLabel33">Delete Slider?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user-delete') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <p>Are you sure you want to delete this user?</p>
                        <input type="hidden" name="user_id" id="user_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Basic toast -->
    <div class="toast toast-basic hide position-fixed" role="alert" aria-live="assertive" aria-atomic="true"
        data-delay="5000" style="top: 1rem; right: 1rem">
        <div class="toast-header">
            <strong class="mr-auto">fleetNG Admin</strong>
            <!-- <small class="text-muted">11 mins ago</small> -->
            <button type="button" class="ml-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">User Login Pin Changed Successfully!</div>
    </div>
    <!-- Basic toast ends -->

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
    <script src="{{ asset(mix('js/scripts/components/components-bs-toast.js')) }}"></script>
    {{-- Page js files --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $.fn.dataTableExt.sErrMode = 'throw';
            $('#userList').DataTable({
                "language": {

                },
                "order": [],
                "processing": true,
                "bFilter": false,
                "bInfo": false,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": false,
                "ajax": {
                    "url": "{{ route('user-list-detail') }}"

                },
                "columns": [

                    {
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name",
                        "orderable": false
                    },
                    {
                        "data": "phone",
                        "orderable": false
                    },
                    {
                        "data": "email",
                        "orderable": false
                    },
                    {
                        "data": "role",
                        "orderable": false
                    },
                    {
                        "data": "last_active",
                        "orderable": false
                    },
                    {
                        "data": "is_active",
                        "orderable": false
                    },
                    {
                        "data": "action",
                        "orderable": false
                    },
                ],

            });
        });

        function newUser() {
            var search_phone = $('#search_phone').val();
            var search_user = $('#search_user').val();
            var search_email = $('#search_email').val();
            var user_type = $('#user_type').val();
            var role = $('#role').val();


            // var daterange = daterange.split("to");
            var table = $('#userList').DataTable();

            table.destroy();
            table = null;
            $.fn.dataTableExt.sErrMode = 'throw';
            $('#userList').DataTable({
                "language": {

                },
                "order": [],
                "processing": true,
                "bFilter": false,
                "bInfo": false,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": false,
                "ajax": {
                    "url": "{{ route('user-list-detail-filter') }}",
                    data: {
                        "search_phone": search_phone,
                        "search_user": search_user,
                        "search_email": search_email,
                        "user_type": user_type,
                        "role": role,
                    }
                },
                "columns": [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "name",
                        "orderable": false
                    },
                    {
                        "data": "phone",
                        "orderable": false
                    },
                    {
                        "data": "email",
                        "orderable": false
                    },
                    {
                        "data": "role",
                        "orderable": false
                    },
                    {
                        "data": "last_active",
                        "orderable": false
                    },
                    {
                        "data": "is_active",
                        "orderable": false
                    },
                    {
                        "data": "action",
                        "orderable": false
                    },
                ],
            });

        }

        function enableactive($data, $status) {
            var id = $data;
            var status = $status

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('user-status') }}',
                type: "POST",
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    // console.log(data);
                    location.reload();
                }
            });

        }


        function get_delete_id(id) {
            $("#user_id").val(id);
        }

        function onlyNumberKey(evt) {

            // Only ASCII character in that range allowed
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }
    </script>
@endsection
