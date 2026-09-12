@extends('layouts/contentLayoutMaster')
@section('title', 'Add Merchant')

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

@php

$login_pin = substr((mt_rand()), 0, 4);

@endphp

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Add Merchant</h4>
          <a href="{{ route('merchant-list') }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
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
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">First Name*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name*" value="{{ old('first_name') }}" required="" />
                      @if ($errors->has('first_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('first_name') }}</span><br>
                      @endif
                    </div>                    
                  </div>
                </div>
                
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Last Name*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name*" value="{{ old('last_name') }}" required="" />
                      @if ($errors->has('last_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('last_name') }}</span><br>
                      @endif
                    </div>                    
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Company Name*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="merchant_name" class="form-control" name="merchant_name" placeholder="Company Name*" value="{{ old('merchant_name') }}" required="" />
                      @if ($errors->has('merchant_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('merchant_name') }}</span><br>
                      @endif
                    </div>                    
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Company Address*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="merchant_address" class="form-control" name="merchant_address" placeholder="Company Address*" value="{{ old('merchant_address') }}" required="" />
                      @if ($errors->has('merchant_address'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('merchant_address') }}</span><br>
                      @endif
                    </div>                    
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Company Phone Number*</label>
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
                      <label for="slider_image_head_two">Company Email ID*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="email_id" class="form-control" name="email" placeholder="Email ID" value="{{ old('email') }}"  required=""/>
                      @if ($errors->has('email'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('email') }}</span><br>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Add</button>
                  <a href="{{ route('merchant-list') }}" class="btn btn-danger mr-1">Cancel</a>

                </div>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
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
  $('#admin_merchant_list').addClass('active');

  
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
