@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Merchant')

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

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Edit Merchant</h4>
          <a href="{{ route('merchant-list') }}" class="btn btn-outline-dark mr-1"><i data-feather='chevron-left'></i> Back</a>
        </div>
        <br>
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
        <div class="card-body">
          <form class="form form-horizontal" method="POST" action="{{ route('edit-merchant',$user_details['id']) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="col-12">
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">First Name*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name*" value="{{$user_details['first_name']}}" required="" />
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
                      <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name*" value="{{$user_details['last_name']}}" required="" />
                      @if ($errors->has('last_name'))
                      <span class="text-danger mb-2 float-left"> *{{ $errors->first('last_name') }}</span><br>
                      @endif
                    </div>                    
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Merchant Name*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="merchant_name" class="form-control" name="merchant_name" placeholder="Merchant Name*" value="{{$user_details['merchant_name']}}" required="" />
                    </div>                   
                  </div>
                </div>

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Merchant Address*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="text" id="merchant_address" class="form-control" name="merchant_address" placeholder="Merchant Address*" value="{{$user_details['merchant_address']}}" required="" />
                    </div>                   
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Phone Number*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="number" id="phone" class="form-control" name="phone" placeholder="Phone Number" value="{{$user_details['phone']}}"  required="" />
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Email*</label>
                    </div>
                    <div class="col-sm-9">
                      <input type="email" id="email" class="form-control" name="email" placeholder="email" value="{{$user_details['email']}}"/>
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-9 offset-sm-3">
                  <button type="submit" class="btn btn-primary mr-1">Update</button>
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
