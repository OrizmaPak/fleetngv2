@extends('layouts/contentLayoutMaster')

@section('title', 'Trip Detail')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
<link href="{{ asset('editor/css/summernote-bs4.css') }}" rel="stylesheet">
@endsection


@section('content')

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Trip Detail</h4>
          <a href="{{ url()->previous() }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>
        <br>
        <div class="card-body">
          <form class="form form-horizontal" method="POST" action="{{ route('add-driver') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">

              <div class="col-12">

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Driver Name</label>
                    </div>
                    <div class="col-sm-9">
                      <a href="{{ route('view-driver',$trip_details['driver']['id']) }}">{{$trip_details['driver']['first_name']}} {{$trip_details['driver']['last_name']}}</a>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Client Name</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{$trip_details["client_name"]}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Date & Time</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{date("d-M-Y h:i A",strtotime($trip_details["trip_generated_at"]))}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Total Trip Cost(in NGN)</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{$trip_details["total_cost"]}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Pickup Location</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{$trip_details["pickup_location"]["location"]}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Drop Location</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{$trip_details["drop_location"]["location"]}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Clock-in Time</label>
                    </div>
                    <div class="col-sm-9">
                      @if($trip_details["clock_in_time"])
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{date("d-M-Y h:i A",strtotime($trip_details["clock_in_time"]))}}'  required="" readonly=""/>
                      @else
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='NA'  required="" readonly=""/>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Clock-out Time</label>
                    </div>
                    <div class="col-sm-9">
                      @if($trip_details["clock_out_time"])
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='{{date("d-M-Y h:i A",strtotime($trip_details["clock_out_time"]))}}'  required="" readonly=""/>
                      @else
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Phone Number" value='NA'  required="" readonly=""/>
                      @endif
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Driver Commission</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Driver Commission" value='{{($trip_details["driver_commission"])?$trip_details["driver_commission"]:"NA"}}'  required="" readonly=""/>
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Trip Status</label>
                    </div>
                    <div class="col-sm-9">

                      @if($trip_details["status"]==1)
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Driver Commission" value='New Trip'  required="" readonly=""/>
                      @elseif($trip_details["status"]==2)
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Driver Commission" value='Live Trip'  required="" readonly=""/>
                      @elseif($trip_details["status"]==3)
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Driver Commission" value='Completed Trip'  required="" readonly=""/>
                      @else
                      <input type="text" id="phone" class="form-control" name="phone" placeholder="Driver Commission" value='Canceled Trip'  required="" readonly=""/>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
          </form>
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

<script src="{{ asset('editor/js/summernote-bs4.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('.summernote').summernote({
      height: 200,
    });
  });
</script>
{{-- Page js files --}}
<script type="text/javascript">
</script>
@endsection
