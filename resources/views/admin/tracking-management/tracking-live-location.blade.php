@extends('layouts/contentLayoutMaster')

@section('title', 'Live Trip Location')

@section('vendor-style')
{{-- vendor css files --}}
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}

{{-- <script src="https://maps.google.com/maps/api/js?key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U"></script> --}}
@endsection
<style type="text/css">
  #map {
    height: 400px;
    width: 500px;
  }
</style>

@section('content')


  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">Live Location</h4>
        </div>

        <div class="card-body">
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two"></label>
                    </div>
                    <div class="col-sm-9">
                    
                      <div id="map"></div>
                      <div id="infowindow-content">
                        <img src="" width="16" height="16" id="place-icon">
                        <span id="place-name" class="title"></span><br>
                        <span id="place-address"></span>
                      </div>
                    </div>
                  </div>
                </div>
               
              </div>
        </div>
      </div>
    </div>
  </div>

<!--/ Ajax Sourced Server-side -->

@endsection

@section('page-script')
{{-- Page js files --}}

<script type="text/javascript">
  //  this function show selected sidebar active
  $('#admin_trip_list').addClass('active');
  //  this function show map s

    var a = 6.4280556;
    var b = 3.4219444;

    function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: a, lng: b},
        zoom: 17
    });

    var latlng = new google.maps.LatLng(a,b); 
            var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            icon: {url: "{{ asset('map-icons/truck.png')}}", scaledSize: new google.maps.Size(40, 40) },
            title: 'Current Location', 
        });
    }


</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U&libraries=places&callback=initMap&loading=async" async defer></script>

@endsection