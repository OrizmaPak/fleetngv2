@extends('layouts/contentLayoutMaster')

@section('title', 'Driver Trip List')

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
          <h4 class="card-title">Driver Trip List</h4>
          <a href="{{ url()->previous() }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table" id="userList">
            <thead>
              <tr>
                <th>id</th>
                <th>Driver Name</th>
                <th>Trip Created</th>
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
        <h4 class="modal-title" id="myModalLabel33">Delete Slider?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('driver-delete') }}" method="post">
        @csrf
        <div class="modal-body text-center">
          <p>Are you sure you want to delete this driver?</p>
          <input type="hidden" name="driver_id" id="driver_id">
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
        //  this function show selected sidebar active
        $('#admin_driver_list').addClass('active');

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
            "url":"{{ route('driver-trip-list-detail',Request::segment(3)) }}"
            
          },
          "columns":[
             
              { "data": "DT_RowIndex" },
              { "data": "name" , "orderable": false},
              { "data": "trip_generated_at" , "orderable": false},
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
        $("#driver_id").val(id);
      }
  </script>
@endsection