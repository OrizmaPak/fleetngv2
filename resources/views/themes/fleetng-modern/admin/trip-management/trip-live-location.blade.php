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
<x-fleetng.page-heading>@yield('title')</x-fleetng.page-heading>
<x-fleetng.feedback />


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
                      {{-- <div id="infowindow-content">
                        <img src="" width="16" height="16" id="place-icon">
                        <span id="place-name" class="title"></span><br>
                        <span id="place-address"></span>
                      </div> --}}
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
  var map ='';
  var marker ='';
    function initMap() {
      var directionsService = new google.maps.DirectionsService();
      var directionsRenderer = new google.maps.DirectionsRenderer();
      var latlng = new google.maps.LatLng( "{{$vehicle_details['lat']}}","{{$vehicle_details['long']}}"); 
      map = new google.maps.Map(document.getElementById('map'), {
          center: latlng,
          zoom: 18
      });
      var request = {
        origin: "{{$trip->pickuplocation->location}}",
        destination: "{{$trip->droplocation->location}}",
        travelMode: 'DRIVING'
      };

      directionsRenderer.setMap(map);
      directionsService.route(request, function(result, status) {
        if (status == 'OK') {
          directionsRenderer.setDirections(result);
        }
      });

      marker = new google.maps.Marker({
          position: latlng,
          map: map,
          animation: google.maps.Animation.BOUNCE,
          icon: {url: "{{ asset('map-icons/truck.png')}}", scaledSize: new google.maps.Size(40, 40) },
          title:'TRUCK ID: {{$vehicle_details["registration_no"]}}',
      });
      const infowindow = new google.maps.InfoWindow({
        content: "<b>TRUCK ID:</b> {{$vehicle_details['registration_no']}}",
        ariaLabel: "TRUCK",
      });

      infowindow.open({
        anchor: marker,
        map,
      });
      marker.setMap(map);
    }

    let intervalID = setInterval(updateMapMarker, 10000);//every 10 seconds
    document.addEventListener("visibilitychange", function() {
      if(document.visibilityState==="hidden"){
          clearInterval(intervalID);
      }
      else if(document.visibilityState==="visible"){
          intervalID = setInterval( updateMapMarker,10000);
      }
    });
    
    function updateMapMarker(){
      //Call ajax here
      $.ajax({
        type: "GET",
        url: "{{ url('trip-live-location/1')}}",
        dataType: "json",
        data: {'serial':{{$vehicle_details['serial']}}},
        beforeSend: function(request) {
          return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr(
            'content'));
        },
          success: function(response) {
            
            if (response.message == "Data Found") {
              var lat = response.vehicle_details.lat;
              var long = response.vehicle_details.long;
              latlng = new google.maps.LatLng(lat, long);
              marker.setPosition(latlng);

            } else if (response.message == "No record found") {
              alert(response.message);
            } else {
              console.log('error in activity ajax call.');
              $('#error_alert').css('display', 'block');
            }
          },
          error: function(error) {
            console.log(error);
            $("#alert_msg1").html('Failed! Please try again.');
          }
      });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U&libraries=places&callback=initMap&loading=async" async defer></script>

@endsection