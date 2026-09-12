@extends('layouts/contentLayoutMaster')

@section('title', 'View Driver')

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
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">View Driver</h4>
          <a href="{{ url()->previous() }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>
        <br>
        <div class="card-body">     
            <div class="row">
              <div class="col-12">
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Full Name*</label>
                    </div>
                    <div class="col-sm-5">
                      <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name*" value="{{$driver_details['first_name']}}" required=""  readonly="" />
                    </div>
                    <div class="col-sm-4">
                      <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name" value="{{$driver_details['last_name']}}" readonly=""/>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Phone Number*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="number" id="phone" class="form-control" name="phone" placeholder="Phone Number" value="{{$driver_details['phone']}}"  required="" readonly=""/>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Photo*</label>
                    </div>
                    <div class="col-sm-9">
                      <img src="@if($driver_details['photo']){{$driver_details['photo']}}@else {{asset('images/user_default.png')}} @endif" height="100"/> 
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Vehicle ID*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="vehicle_id" class="form-control" name="vehicle_id" placeholder="Vehicle ID" value="{{$driver_details['vehicle_id']}}"  required="" readonly=""/>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Login PIN*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" value="****"  required="" readonly=""/>
                    </div>
                  </div>
                </div>
              </div>
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
<script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
  //  this function show selected sidebar active
  $('#admin_driver_list').addClass('active');
</script>

@endsection
