@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Pickup Location')

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
<style type="text/css">
  #map {
    height: 400px;
    width: 100%;
  }
</style>

@section('content')
<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>
<x-fleetng.feedback />

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Edit Pickup Location</h4>
          <a href="{{ route('location-list') }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>
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
        <div class="card-body">
          <form class="form form-horizontal" method="POST" action="{{ route('edit-pickup-location',$location_details['id']) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">

              <div class="col-12">

                <div class="col-12">
                  <label for="slider_image_head_two">Pickup Location*</label>
                  <div class="form-group row">
                    {{-- <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Pickup Location*</label>
                    </div> --}}
                    <div class="col-sm-12">
                      <!-- <textarea name="location" class="form-control" required=""></textarea> -->
                      <input class="form-control" type="text" name="location_name" value="{{$location_details['location_name']}}" placeholder="Enter address name" autocomplete="off" required><br>
                      <input class="form-control" id="pac-input" type="text" name="location" parsley-trigger="change" value="{{$location_details['location']}}" placeholder="Enter address" autocomplete="off">
                    </div>
                  </div>
                </div>



                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two"></label>
                    </div>
                    <div class="col-sm-12">
                      @include('admin.location-management.coordinates')
                    </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Update</button>
                  <a href="{{ route('location-list') }}" class="btn btn-danger mr-1">Cancel</a>

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
<script>$('#admin_location_list').addClass('active');</script>
@endsection
