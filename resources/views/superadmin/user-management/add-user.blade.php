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
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Merchantc*</label>
                    </div>
                    <div class="col-sm-9">  
                    <select class="form-control" name="merchant" id="merchant" 
                    {{-- onchange="getMerchant()" --}}
                     required>
                      <option value="">Select Merchant</option>
                    @if(isset($merchant_details))
                     @foreach($merchant_details as $merchant)
                      <option value="{{ $merchant['id'] }}"> {{ $merchant['merchant_id'] }} - {{ $merchant['merchant_name'] }}</option>
                      @endforeach
                      @endif
                    </select>
                      @if ($errors->has('merchant'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('merchant') }}</span><br>
                      @endif
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
                    </div>
                    <div class="col-sm-9">
                      <div class="form-check">
                        <input type="checkbox" id="payment_user" class="form-check-input" name="payment_user"/>
                        <label for="payment_user" class="form-check-label">Payment User</label>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Add</button>
                  <a href="{{ route('user-list') }}" class="btn btn-danger mr-1">Cancel</a>

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

<script src="{{URL::to('js/jquery.imgareaselect.js')}}"></script>
<script src="{{URL::to('js/croppie.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript">
  //  this function show selected sidebar active
  $('#admin_user_list').addClass('active');

  //get merchant data
  function getMerchant(){
    var id=$('#merchant').val();
    // alert();
    $.ajax({
      type:'get',
      url:"{{route('merchant')}}",
      data:{id:id},
      success:function(data) {
        if(data.first_name != ''){
          $('#first_name').val(data.first_name);
        }
        if(data.last_name != ''){
          $('#last_name').val(data.last_name);
        }
        if(data.email != ''){
          $('#email_id').val(data.email);
        }
        if(data.phone != ''){
          $('#phone').val(data.phone);
        }
      }
  });
  }
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
        // $('#pre_image').attr('src', e.target.result);
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