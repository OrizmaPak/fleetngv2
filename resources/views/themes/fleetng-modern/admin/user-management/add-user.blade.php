@extends('layouts/contentLayoutMaster')

@section('title', 'Add User')

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
<link rel="stylesheet" href="{{asset('css/croppie.css')}}">
@endsection


@section('content')
<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>
<x-fleetng.feedback />

@php

$login_pin = substr((mt_rand()), 0, 4);

@endphp

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Add User</h4>
          <a href="{{ route('user-list') }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
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
          <form class="form form-horizontal" method="POST"  enctype="multipart/form-data">
            @csrf
            <div class="row">

                <div class="col-12">
                  <div class="row mb-2 form-group">

                    {{-- this is for admin] --}}
                    <div class="col-sm-3">
                      <label for="merchant">Merchant*</label>
                    </div> 
                    <div class="col-sm-9">

                      <select name="merchant" class="form-control" >
                        <option disabled selected >--Select Merchant--</option>
                        @forelse($merchants as $merchant)
                          <option value="{{ $merchant->id }}" {{ old('merchant') == $merchant->id ? 'selected' : '' }}> {{ $merchant->merchant_id.'-'.$merchant->merchant_name }}</option>
                        @empty
                        @endforelse
                      </select>
                      @error('merchant')
                          <div class="error">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Full Name*</label>
                    </div>
                    <div class="col-sm-5">
                      <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name*" value="{{ old('first_name') }}" required="" />
                      @if ($errors->has('first_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('first_name') }}</span><br>
                      @endif
                    </div>
                    <div class="col-sm-4">
                      <input type="text" id="last_name" class="form-control" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" />
                      @if ($errors->has('last_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('last_name') }}</span><br>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Phone Number*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="number" id="phone" class="form-control" name="phone" placeholder="Phone Number" value="{{ old('phone') }}"  required="" />
                      @if ($errors->has('phone'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('phone') }}</span><br>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Email ID*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="email_id" class="form-control" name="email" placeholder="Email ID" value="{{ old('email') }}"  required=""/>
                      @if ($errors->has('email'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('email') }}</span><br>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Photo*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="file" name="photo" id="profile_image" class="form-control" accept="image/*" required="" onchange="preview_image(this)">
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Crop Image*</label>
                    </div>
                    <div class="col-sm-9">
                      <div class="row align-items-center">
                    <div class="col-sm-5">
                      <div id="pre_image" style="width:350px"></div>
                    </div>
                    <div class="col-sm-3">
                      <center class="block-1-line-height"><a href="javascript::void(0);" class="btn btn-info crop_image mt-10">Crop</a></center>
                    </div>
                    <div class="col-sm-4">
                      <div id="crop_image_view" class="block-2-line-height" name=""></div>
                    </div>
                  </div>
                  <span id="alert_crop_image" class="text-danger"></span>
                  <input type="hidden" name="crop_image_id" id="crop_image_id" >
                    </div>
                  </div>
                </div>
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Add</button>
                  <a href="{{ route('user-list') }}" class="btn btn-danger mr-1">Cancel</a>

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

<script src="{{URL::to('js/jquery.imgareaselect.js')}}"></script>
<script src="{{URL::to('js/croppie.js')}}"></script>

<script src="{{ asset('editor/js/summernote-bs4.js') }}"></script>
<script type="text/javascript">
  //  this function show selected sidebar active
  $('#admin_user_list').addClass('active');

  
  //this function show crop image 
  $uploadCrop = $('#pre_image').croppie({
    enableExif: true,
    viewport: {
      width: 100,
      height: 100,
      type: 'circle'
    },
    boundary: {
      width: 200,
      height: 200
    }
  });

  function preview_image(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $uploadCrop.croppie('bind', {
          url: e.target.result
        }).then(function() {
          console.log('jQuery bind complete');
        })
      }

      reader.readAsDataURL(input.files[0]);
    }

  } //close preview_image function


  $('.crop_image').on('click', function(ev) {
    $uploadCrop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(resp) {

      html = '<img src="' + resp + '"/>';
      $('#crop_image_view').html(html);
      $('#crop_image_id').val(resp);
      console.log($('#crop_image_id').val());

    });
  }); 


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
