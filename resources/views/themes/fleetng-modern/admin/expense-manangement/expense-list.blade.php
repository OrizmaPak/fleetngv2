@extends('layouts/contentLayoutMaster')
@if (Auth::user()->user_type == 1)
    @section('title', 'Expense Management')
@else
    @section('title', 'Expenses')
@endif

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
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
                        <div class="">
                            <h4 class="card-title">Expenses</h4>
                            <h6>Total - <span id="total_expense">0</span> NGN</h6>
                        </div>
                        <div class="">
                            <input type="text" id="date_range" class="form-control flatpickr-range" onchange="getList()"
                                placeholder="YYYY-MM-DD to YYYY-MM-DD" style="width: 200px;">
                        </div>
                        <div class="">
                            <input type="text" class="form-control" onkeyup="getList()" id="driver_phone"
                                placeholder="Search By Driver Phone">
                        </div>

                        <div class="">
                            <input type="text" class="form-control" onkeyup="getList()" id="item_name"
                                placeholder="Search By Item Name">
                        </div>
                        <div class="">
                            <select class="form-control" name="" id="driver_id" onchange="getList()">
                                <option value="">Choose Driver</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-datatable table table-responsive">
                        <table class="datatables-ajax table" id="expenseList">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Expense Created On</th>
                                    <th>Driver Name</th>
                                    <th>Driver Phone Number</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Item Cost (NGN)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade text-left" id="deleteExpenseConfirm" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Delete Expense?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('expense-delete') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <p>Are you sure you want to delete this expense?</p>
                        <input type="hidden" id="expense_id" name="expense_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="editExpenseModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Expense</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('expense-edit') }}" method="post">
                    @csrf
                    <div class="modal-body text-center">
                        <input class="form-control" id="expense_item_name" name="item_name"
                            placeholder="Expense Item" />
                        <br>
                        <input type="number" class="form-control" id="item_cost" name="item_cost"
                            placeholder="Expense Amount">
                        <br>
                        <input type="number" class="form-control" id="item_quantity" name="item_quantity"
                            placeholder="Expense Quantity">
                        <br>
                        <input type="hidden" id="edit_expense_id" name="expense_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
                    </div>
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

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            getList();
        });

        function getList() {
            var phone = $('#driver_phone').val();
            var item_name = $('#item_name').val();
            var driver_id = $('#driver_id').val();
            var daterange = $('#date_range').val();
            var table = $('#expenseList').DataTable();

            table.destroy();
            table = null;
            $.fn.dataTableExt.sErrMode = 'throw';
            $('#expenseList').DataTable({
                "order": [],
                "processing": true,
                "bFilter": false,
                "bInfo": false,
                "ordering": true,
                "serverSide": true,
                "bLengthChange": false,
                "ajax": {
                    "url": "{{ route('expenses-list') }}",
                    data: {
                        "phone": phone,
                        "item_name": item_name,
                        "driver_id": driver_id,
                        "daterange": daterange
                    },
                    complete: function(data) {
                        $('#total_expense').text(data.responseJSON.total_expenses); // Display total expenses
                    }
                },
                "columns": [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "created_at",
                        "orderable": true
                    },
                    {
                        "data": "name",
                        "orderable": true
                    },
                    {
                        "data": "phone_number",
                        "orderable": true
                    },
                    {
                        "data": "item_name",
                        "orderable": true
                    },
                    {
                        "data": "item_quantity",
                        "orderable": true
                    },
                    {
                        "data": "item_cost",
                        "orderable": true
                    },
                    {
                        "data": "action",
                        "orderable": false
                    },
                ],
            });
        }

        function editExpense(id) {
            var element = $('#edit-expense-' + id);
            $('#edit_expense_id').val(id);
            $('#expense_item_name').val(element.data('name').trim());
            $('#item_quantity').val(element.data('quantity'));
            $('#item_cost').val(element.data('cost'));
            $('#editExpenseModal').modal('show');
        }

        function deleteAction(id) {
            $('#expense_id').val(id);
        }
    </script>
@endsection
