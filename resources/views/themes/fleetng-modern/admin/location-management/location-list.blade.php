
@extends('layouts/contentLayoutMaster')

@section('title', 'Geofencing')

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
          <h4 class="card-title">Geofencing</h4>
          @if(Session::get('user_role')==2)
          <a href="{{ route('add-pickup-location') }}" class="btn btn-success waves-effect waves-float waves-light"><i data-feather='plus'></i>&nbsp;Add New Pickup Location</a>
          @endif
           <a href="{{route('add-pickup-location')}}"><button class="btn btn-primary btn-sm">Add Location</button></a>
        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table" id="pickupLocationTable">
            <thead>
              <tr>
                <th>id</th>
                {{-- <th>Pickup Location</th> --}}
                <th>Pickup Location Name</th>
                <th>Company User</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>


    <!-- <div class="col-6">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Drop Location List</h4>
          <a href="{{ route('add-pickup-location') }}" class="btn btn-success waves-effect waves-float waves-light"><i data-feather='plus'></i>&nbsp;Add New Pickup Location</a>
        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table" id="dropLocationTable">
            <thead>
              <tr>
                <th>id</th>
                <th>Location</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div> -->

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
        <h4 class="modal-title" id="myModalLabel33">Delete Location?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('pickup-location-delete') }}" method="post">
        @csrf
        <div class="modal-body text-center">
          <p>Are you sure you want to delete this location?</p>
          <input type="hidden" name="location_id" id="location_id">
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
@endsection

@section('page-script')
  {{-- Page js files --}}
  <script type="text/javascript">
      
      $(document).ready(function() {
        $.fn.dataTableExt.sErrMode = 'throw';
        $('#pickupLocationTable').DataTable({
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
            "url":"{{ route('pickup_location-list-detail') }}"
            
          },
          "columns":[
             
              { "data": "DT_RowIndex" },
              // { "data": "location" , "orderable": false},
              { "data": "location_name","orderable": false},
              { "data": "company_user" , "orderable": false},
              { "data": "is_active" , "orderable": false},
              { "data": "action" , "orderable": false},
            ],
          
          });        
  });


      //     $(document).ready(function() {
      //       $.fn.dataTableExt.sErrMode = 'throw';
      //       $('#dropLocationTable').DataTable({
      //         "language": {
              
      //       },
      //         "order":[],
      //          "processing": true,
      //          "bFilter":false,
      //          "bInfo":false,
      //         "ordering":true,
      //         "serverSide": true,
      //         "bLengthChange":false,
      //         "ajax": {
      //           "url":"{{ route('pickup_location-list-detail') }}"
                
      //         },
      //         "columns":[
                 
      //             { "data": "DT_RowIndex" },
      //             { "data": "location" , "orderable": false},
      //             { "data": "action" , "orderable": false},
      //           ],
              
      //         });        
      // });



      function enableactive($data,$status)
      {
        var id = $data;
        var status = $status
        
        $.ajax({
               headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route("pickup-location-status") }}',
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
        $("#location_id").val(id);
      }

  </script>
@endsection
