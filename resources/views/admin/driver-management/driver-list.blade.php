@extends('layouts/contentLayoutMaster')
@if(Auth::user()->user_type == 1)
@section('title', 'Driver Management')
@else
@section('title', 'Drivers')
@endif

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
@php 
$permission=Config('custom.permission');
$super_admin_permission=Config('custom.super_admin_permission');
$user_type=Session::get('user_role');
@endphp
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
  <button class="btn btn-outline-primary toast-basic-toggler mt-2" style="display:none;" id="click_me">Toast</button>
  
  <div class="row"> 
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom gap-1">
          <h4 class="card-title">Driver List</h4>
          {{-- <input type="text" id="search_driver" placeholder="Search Driver" onkeyup="newDriver();" class="form-control" style="width: 150px;">
          <input type="text" id="company_user" placeholder="Company User" onkeyup="newDriver();" class="form-control" style="width: 150px;"> --}}
          @if (!in_array(Auth::user()->user_type,[3,4]))
            
            <div class="col-sm-6 col-md-3">
              <select class="form-control" id="merchant_id" onchange="getUser()">
                <option value="">Merchant</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3">
              <select class="form-control" id="user_id" onchange="filter()">
                <option value=" ">User</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3">
              <input type="text" id="search_vehicle" placeholder="Search Vehicle" onkeyup="filter();" class="form-control" autocomplete="nope">
            </div>
          @endif
          {{-- <select class="form-control" id="status" style="width: 160px;" onchange="filter();">
            <option value=" ">Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select> --}}
          @if(Auth::user()->user_type == $super_admin_permission)
            <a href="{{ route('add-driver') }}" class="btn btn-success waves-effect waves-float waves-light"><i data-feather='plus'></i>&nbsp;Add New Driver</a>
          @endif
        </div>

        <div class="card-datatable table table-responsive">
          <table class="datatables-ajax table" id="userList">
            <thead>
              <tr>
                <th>id</th>
                <th>Driver Name</th>
                <th>Merchant Name</th>
                <th>User Name</th>
                <th>Phone</th>
                <th>Trips</th>
                <th width="150">Login Pins</th>
                <th>Vehicle ID</th>
                <th>Last Active</th>
                <th>Status</th>
                @if(Auth::user()->user_type == $super_admin_permission)
                  <th>Action</th>
                @endif
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
<div class="modal fade text-left" id="deleteSliderConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
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

<!-- Basic toast -->
<div
  class="toast toast-basic hide position-fixed"
  role="alert"
  aria-live="assertive"
  aria-atomic="true"
  data-delay="5000"
  style="top: 1rem; right: 1rem"
>
  <div class="toast-header">
    <strong class="mr-auto">fleetNG Admin</strong>
    <!-- <small class="text-muted">11 mins ago</small> -->
    <button type="button" class="ml-1 close" data-dismiss="toast" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">Driver Login Pin Changed Successfully!</div>
</div>
<input type="hidden" id="user_type" value="{{Auth::user()->user_type}}">
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
  filter();
  $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route("merchant-data") }}',
        type: "POST",
        data: {},
        success: function(data) {
          var option='';
          option+='<option value="">Merchant</option>';
          $.each(data,function(key,value){
            option+='<option value="'+value.id+'">'+value.merchant_id+'</option>';
          })
          $('#merchant_id').html(option);
        }
      });

      function getUser(){
      var id=$('#merchant_id').val();
      $.ajax({
        type:'get',
        url:"{{route('user-list-data')}}",
        data:{id:id},
        success:function(data) {
          console.log(data);
          var option='';
          if(data != ''){
          $.each(data,function(key,value){
            option+='<option value="'+value.id+'">'+value.first_name+' '+value.last_name+'</option>';
          });
        }else{
          option+='<option value="">--Select User--</option>';
        }
          $('#user_id').html(option);
          filter();
        }
    });
    }

  var user_type=$('#user_type').val();
  function filter(){
    if($('#user_type').val() == {{$super_admin_permission}} || $('#user_type').val() == 4 ){ //for superadmin and merchant
      SuperAdminnewDriver();
    }else{
      newDriver();
    }
  }

  function newDriver() {
        var merchant_id = $('#merchant_id').val();
        var search_vehicle = $('#search_vehicle').val();
        var status=$('#status').val() || `{{ request('isActive') }}`;
        var user = $('#user_id').val();
        if(user == ''){
          var user="{{ $user_id }}";
        }
        // alert(user);
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
            "url": "{{ route('driver-list-detail') }}",
            data: {
              "merchant_id": merchant_id,
              "search_vehicle":search_vehicle,
              "company_user":user,
              "user_id":user,
              "status":status
            }
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
          "data": "merchant_name",
          "orderable": false
        },
        {
          "data":"user_name",
          "orderable":false
        },
        {
          "data": "phone",
          "orderable": false
        },
        {
          "data": "total_trips",
          "orderable": false
        },
        {
          "data": "auth_pin",
          "orderable": false
        },
        {
          "data": "vehicle_id",
          "orderable": false
        },
        {
          "data": "last_active",
          "orderable": false
        },
        {
          "data": "is_active",
          "orderable": true
        },
      ],

    });

  }

      function SuperAdminnewDriver() {
        var merchant_id = $('#merchant_id').val();
        var search_vehicle = $('#search_vehicle').val();
        var user = $('#user_id').val();
        var status=$('#status').val() || `{{ request('isActive') }}`;
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
            "url": "{{ route('driver-list-detail') }}",
            data: {
              "merchant_id": merchant_id,
              "search_vehicle":search_vehicle,
              "company_user":user,
              "user_id":user,
              "status":status
            }
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
          "data": "merchant_name",
          "orderable": false
        },
        {
          "data":"user_name",
          "orderable":false
        },
        {
          "data": "phone",
          "orderable": false
        },
        {
          "data": "total_trips",
          "orderable": false
        },
        {
          "data": "auth_pin",
          "orderable": false
        },
        {
          "data": "vehicle_id",
          "orderable": false
        },
        {
          "data": "last_active",
          "orderable": false
        },
        {
          "data": "is_active",
          "orderable": true
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
      url: '{{ route("driver-status") }}',
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
    $("#driver_id").val(id);
  }

  function onlyNumberKey(evt) {

    // Only ASCII character in that range allowed
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
      return false;
    return true;
  }


  function save_pin(id,old_pin) {
    // alert(old_pin);
    var login_pin = $('#login_pin_input'+id).val();
    if(login_pin!='' && (login_pin.length==4) && (login_pin!=old_pin))
    {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route("change-login-pin") }}',
        type: "POST",
        data: {
          id: id,
          login_pin: login_pin
        },
        success: function(data) {
          // location.reload();
          $("#click_me").click();
        }
      });
    }
    

  }
</script>
@endsection