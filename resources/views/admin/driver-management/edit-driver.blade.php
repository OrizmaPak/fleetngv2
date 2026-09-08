@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Driver')

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

<style>
  .error{
    color:red;
  }
</style>
@section('content')

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Edit Driver</h4>
          <a href="{{ route('driver-list') }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>

        {{-- start error msg  --}}
        @if(Session::get('fail'))
          <div class="demo-spacing-0">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <div class="alert-body text-center">
                {{ Session::get('fail') }}
              </div>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        @endif
        {{-- end error msg --}}

        <br>
        <div class="card-body">
          <form class="form form-horizontal" method="POST" action="{{ route('edit-driver',$driver_details['id']) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">

              <div class="col-12">
                @if(Auth::user()->user_type == 1)
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Merchant*</label>
                    </div>
                    <div class="col-sm-5"> 
                      <select name="merchant" id="merchant" class="form-control" onchange="getUser()">
                        <option disabled selected>--Select Merchant--</option>
                          @if(isset($merchant_details))
                            @foreach($merchant_details as $merchant)
                              <option value="{{ $merchant['id'] }}" {{ $driver_details['merchant_id'] == $merchant['id'] ? 'selected' : '' }}> {{ $merchant['merchant_id'] }} - {{ $merchant['merchant_name'] }}</option>
                            @endforeach
                          @endif
                      </select>
                      @error('merchant')
                        <div class="error">{{ $message }}</div>
                    @enderror
                      {{-- <input type="text" id="merchant" class="form-control" name="merchant" placeholder="First Name*" value="{{ old('first_name') }}" required="" /> --}}
                    </div>
                    <div class="col-sm-4">
                      <select name="user_id" id="user" class="form-control">
                        <option value="">--Select User--</option>
                      </select>
                      @error('user_id')
                        <div class="error">{{ $message }}</div>
                     @enderror
                      {{-- <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" /> --}}
                    </div>
                  </div>
                </div>
                @endif
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Full Name*</label>
                    </div>
                    <div class="col-sm-5">
                      <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name*" value="{{$driver_details['first_name']}}"/>
                     @error('first_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    </div>
                    <div class="col-sm-4">
                      <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name" value="{{$driver_details['last_name']}}" />
                    
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Phone Number*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="number" id="phone" class="form-control" name="phone" placeholder="Phone Number" value="{{$driver_details['phone']}}"  required="" />
                    @error('phone')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Photo</label>
                    </div>
                    <div class="col-sm-9">
                      <img src="@if(@getimagesize($driver_details['photo'])){{$driver_details['photo']}}@else {{asset('images/user_default.png')}} @endif" height="100"/>  <br><br>
                      <input type="file" name="photo" id="profile_image" accept="image/*" class="form-control" onchange="preview_image(this)">
                    </div>
                  </div>
                </div>


                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Crop Image</label>
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
                  <input type="hidden" name="crop_image_id" id="crop_image_id" value="" required="">
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Vehicle ID*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="vehicle_id" class="form-control" name="vehicle_id" placeholder="Vehicle ID" value="{{$driver_details['vehicle_id']}}"  required=""/>
                    @error('vehicle_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Device Serial Number (IMEI)*</label>
                    </div>
                    <div class="col-sm-9">
                     <input type="number" id="device_serial_number" class="form-control" name="device_serial_number" placeholder="Device Serial Number (IMEI)" value="{{$driver_details['device_serial_number']}}"  required="" />
                    @error('device_serial_number')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Voice Number*</label>
                    </div>
                    <div class="col-sm-9">
                     <input type="number" id="voice_number" class="form-control" name="voice_number" placeholder="Voice Number" value="{{$driver_details['voice_number']}}"  required="" />
                    @error('voice_number')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Billing Term*</label>
                    </div>
                    <div class="col-sm-9">
                     <input type="number" id="billing_term" class="form-control" name="billing_term" placeholder="Billing Term" value="{{$driver_details['billing_term']}}"  required="" />
                    @error('billing_term')
                        <div class="error">{{ $message }}</div>
                    @enderror
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



                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Update</button>
                  <a href="{{ route('driver-list') }}" class="btn btn-danger mr-1">Cancel</a>

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
    $('#admin_driver_list').addClass('active');

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
 //get merchant data
 var mid=$('#merchant').val();
  if(mid != ''){
    getUser();
  }
  function getUser(){
    var id= $('#merchant').val();
    
    $.ajax({
      type:'get',
      url:"{{route('user-list-data')}}",
      data:{id:id},
      success:function(data) {
        var option='';
        if(data != ''){
          let merchant_ ="{{$driver_details['user_id']}}";
        $.each(data,function(key,value){
          if("{{Auth::user()->user_type}}" == 4){
            
            if(value.id == merchant_){
              option+='<option value="'+value.id+'" selected>'+value.first_name+' '+value.last_name+'</option>';
            }else{
              option+='<option value="'+value.id+'">'+value.first_name+' '+value.last_name+'</option>';
            }

          }else{
            option+='<option value="'+value.id+'"';
            if("{{ $driver_details['user_id'] }}" == value.id){
              option+='selected';
            }
            option+='>'+value.first_name+' '+value.last_name+'</option>';
          }
        });
      }else{
        option+='<option disabled selected>--No User--</option>'; //disabled select user option if there is no user is created by that selected merchant
      }
        $('#user').html(option);
      }
  });
  }
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
      // console.log(resp);
      $('#crop_image_view').html(html);
      $('#crop_image_id').val(resp);
    });
  }); 


  $(document).ready(function() {
    $('.summernote').summernote({
      height: 200,
    });
  });
</script>
{{-- Page js files --}}

@endsection