
@extends('layouts/contentLayoutMaster')

@section('title', 'Tracking Management')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
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
              @if(Session::get('success'))
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
              @if(Session::get('fail'))
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
        <div class="card-header border-bottom">
          <h4 class="card-title">Tracking List</h4> 
          <input type="text" id="date_range" class="form-control flatpickr-range"  onchange="newTrip();" placeholder="YYYY-MM-DD to YYYY-MM-DD" style="width: 200px;">
          <input type="text" id="search_driver" placeholder="Search Driver" onkeyup="newTrip();" class="form-control" style="width: 150px;">
          <input type="text" id="search_location" placeholder="Search Location" onkeyup="newTrip();" class="form-control" style="width: 150px;">
          <select class="form-control" id="trip_type" style="width: 160px;" onchange="newTrip();">
            <option value="all">All Trips</option>
            <option value="1">New Trips</option>
            <option value="2">Live Trips</option>
            <option value="3">Completed Trips</option>
            <option value="4">Canceled Trips</option>
          </select>
        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive" id="userList">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Driver</th>
                <th>Client</th>
                <th>Pickup</th>
                <th>Drop off</th>
                <th>Cost(in NGN)</th>
                <th>Commission(in NGN)</th>
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
<div
  class="modal fade text-left"
  id="deleteSliderConfirm"
  tabindex="-1"
  role="dialog"
  aria-labelledby="myModalLabel33"
  aria-hidden="true"
>
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
          <input type="hidden" name="trip_id" id="trip_id">
          <button type="submit" class="btn btn-primary">Yes</button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">No</button>
        </div>
        
      </form>
    </div>
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
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script type="text/javascript">

      $(document).ready(function() {
        $.fn.dataTableExt.sErrMode = 'throw';
        $('#userList').DataTable({
          "language": { 
        },
          "order":[],
           "processing": true,
           "bFilter":false,
           "bInfo":false,
          "ordering":true,
          "serverSide": true,
          "bLengthChange":false,
          "ajax": {
            "url":"{{ route('trip-list-detail') }}"
            
          },
          "columns":[
             
              { "data": "DT_RowIndex" },
              { "data": "trip_generated_at" , "orderable": true},
              { "data": "name" , "orderable": false},
              { "data": "client_name" , "orderable": false},
              { "data": "pickup" , "orderable": false},
              { "data": "drop_off" , "orderable": false},
              { "data": "total_cost" , "orderable": true},
              { "data": "driver_commission" , "orderable": true},
              { "data": "status" , "orderable": false},
              { "data": "action" , "orderable": false},
            ],
          
          });        
  });



      function enableactive($data,$status)
      {
        var id = $data;
        var status = $status
        
        $.ajax({
               headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("driver-status") }}',
                type: "POST",
                data: {id:id,status:status},
                success: function (data) {
                    // console.log(data);
                  location.reload();
                }
              });

      }


      function get_delete_id(id)
      {
        $("#trip_id").val(id);
      }

     

      function newTrip() {
        var trip_type = $('#trip_type').val();
        var search_driver = $('#search_driver').val();
        var search_location = $('#search_location').val();
        var daterange = $('#date_range').val();

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
            "url": "{{ route('trip-list-detail-filter') }}",
            data: {
              "trip_type": trip_type,
              "search_driver":search_driver,
              "search_location":search_location,
              "daterange":daterange
            }
          },
          "columns": [
             
              { "data": "DT_RowIndex" },
              { "data": "trip_generated_at" , "orderable": false},
              { "data": "name" , "orderable": false},
              { "data": "client_name" , "orderable": false},
              { "data": "pickup" , "orderable": false},
              { "data": "drop_off" , "orderable": false},
              { "data": "total_cost" , "orderable": false},
              { "data": "driver_commission" , "orderable": false},
              { "data": "status" , "orderable": false},
              { "data": "action" , "orderable": false},
            ],

        });

      }


  </script>
@endsection
