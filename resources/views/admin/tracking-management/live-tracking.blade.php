
@extends('layouts/contentLayoutMaster')

@section('title', 'Tracking Management')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

<style>
.dt-buttons {
    float: right;
    position: relative;    
    padding: 10px;
    margin-top: -112px;
}   
.stopped {  
  background-image:  url("{{ asset('map-icons/red.png') }}"); 
  background-repeat: no-repeat; 
  background-size: contain; 
  background-position: center;
  width:50px;
  height:50px; 
  color: transparent; 
  display:block;   
} 
.idling {  
  background-image: url("{{ asset('map-icons/yellow.png') }}"); 
  background-repeat: no-repeat; 
  background-size: contain;  
  background-position: center;
  width:50px;
  height:50px;  
  color: transparent;  
  display:block;  
} 
.moving {  
  background-image: url("{{ asset('map-icons/green.png') }}"); 
  background-repeat: no-repeat; 
  background-size: contain;
  background-position: center;
  width:50px;
  height:50px;     
  color: transparent;  
  display:block;  
} 
.unreachable {  
  background-image: url("{{ asset('map-icons/black.png') }}"); 
  background-repeat: no-repeat;   
  background-size: contain;
  background-position: center; 
  width:50px;
  height:50px;    
  color: transparent;  
  display:block;  
} 
.inactive {  
  background-image: url("{{ asset('map-icons/grey.png') }}");
  background-repeat: no-repeat;
  background-size: contain; 
  background-position: center;
  width:50px;
  height:50px;    
  color: transparent; 
  display:block;   
}   
    </style>
    
@section('content')

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
        <div class="card-header border-bottom flex-column">
          <div class="d-flex align-items-center justify-content-between w-100 mb-1">
            <h4 class="card-title">Live Tracking</h4> 
            <div class="btn-toolbar mb-2 mb-md-0">
              <div class="me-2 print-options-btn" onclick="newTrip(event);">
                <button type="button" class="btn btn-sm btn-outline-secondary btn-warning" data-value="1">Idling</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-success" data-value="2">Moving</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-danger" data-value="0">Stopped</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-dark" data-value="4">Unreachable</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-secondary" data-value="5">Inactive</button>
              </div>
            </div>
          </div>
          <div class="w-100 d-flex align-items-center justify-content-between">
            <input type="text" id="search_driver" placeholder="Search" onkeyup="newTrip();" class="form-control" style="max-width: 250px;">
            <select class="form-control" id="vehicle_status" style="width: 160px;" onchange="newTrip();">
              <option value="6">All</option>
              <option value="0">Stopped</option>
              <option value="1">Idling</option>
              <option value="2">Moving</option>
              <option value="4">Unreachable</option>
              <option value="5">Inactive</option>
            </select>
          </div>
        </div>

        <div class="card-datatable">
          <table class="datatables-ajax table table-responsive live-tracking-table" id="userList">
            <thead>
              <tr>
                <th scope="col">Truck ID</th>
                <th scope="col">Status</th>
                <th scope="col">Device Serial Number (IMEI)</th>
                <th scope="col">Voice Number</th>
                <th scope="col">Billing Term</th>
                <th scope="col">Driver Name</th>
                <th scope="col">Last Updated</th>
                <th scope="col">Installation Date</th>
                <th scope="col">Distance</th>               
                <th scope="col">Location</th>
              </tr>
            </thead>           
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
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
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
            "url":"{{ route('tracking-list-detail') }}",
            "data":{
              'u_ID':"{{Auth::user()->id}}"
            }
            
          },
          "columns":[
              { "data": "truck_id" , "orderable": true},
              { "data": "status" , "orderable": false},
              { "data": "device_serial_number" , "orderable": false},
              { "data": "voice_number" , "orderable": false},
              { "data": "billing_term" , "orderable": false},
              { "data": "driver_name" , "orderable": false},
              { "data": "last_updated" , "orderable": false},
              { "data": "installation_date" , "orderable": true},
              { "data": "distance" , "orderable": true},              
              { "data": "location" , "orderable": false},
            ],          
          });        
  });

      function newTrip(e=null) {
        var vehicle_status = $('#vehicle_status').val();
        if(e){
          vehicle_status   = $(e.target).data('value');
        }
        var search_driver = $('#search_driver').val();
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
            "url": "{{ route('tracking-list-detail-filter') }}",
            data: {
              "vehicle_status": vehicle_status,
              "search_driver":search_driver,
              'u_ID':"{{Auth::user()->id}}"
            }
          },
          "columns": [
              { "data": "truck_id" , "orderable": true},
              { "data": "status" , "orderable": false},
              { "data": "device_serial_number" , "orderable": false},
              { "data": "voice_number" , "orderable": false},
              { "data": "billing_term" , "orderable": false},
              { "data": "driver_name" , "orderable": false},
              { "data": "last_updated" , "orderable": false},
              { "data": "installation_date" , "orderable": true},
              { "data": "distance" , "orderable": true},              
              { "data": "location" , "orderable": false},
            ],
        });
      }
  </script>
@endsection