@extends('layouts/contentLayoutMaster')

@section('title', 'View Pickup Location')

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
{{-- <script src="https://maps.google.com/maps/api/js?sensor=false&&key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U"></script> --}}
@endsection
<style type="text/css">
  #map {
    height: 400px;
    width: 500px;
  }
</style>

@section('content')

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">View Pickup Location</h4>
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
            <div class="row">

              <div class="col-12">

                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two">Pickup Location*</label>
                    </div>
                    <div class="col-sm-9">
                      <!-- <textarea name="location" class="form-control" required=""></textarea> -->
                      <input class="form-control" type="text" value="{{$location_details['location_name']}}" readonly><br>
                      <input class="form-control" id="pac-input" type="text" name="location" parsley-trigger="change" value="{{$location_details['location']}}" placeholder="Enter address" autocomplete="off" readonly>
                    </div>
                  </div>
                </div>



                <div class="col-12">
                  <div class="form-group row">
                    <div class="col-sm-3 col-form-label">
                      <label for="slider_image_head_two"></label>
                    </div>
                    <div class="col-sm-9">
                      <input id='loc' type='hidden'  name="loc"  value='{{(isset($location_details["latitude"]))?$location_details["latitude"]:""}}, {{(isset($location_details["longitude"]))?$location_details["longitude"]:""}}' />
                      <br>
                      <div id="map"></div>
                      <div id="infowindow-content">
                        {{-- <img src="" width="16" height="16" id="place-icon"> --}}
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


<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

{{-- Page js files --}}

   <script type="text/javascript">
      //  this function show selected sidebar active
      $('#admin_location_list').addClass('active');


        var a = {{$location_details["latitude"]}};
        var b = {{$location_details["longitude"]}};

        function initMap() { 
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: a, lng: b},
          zoom: 17
        });
        var input = document.getElementById('pac-input');//only required
        

        var autocomplete = new google.maps.places.Autocomplete(input);

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        autocomplete.bindTo('bounds', map);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29),
          draggable: true //this makes it drag and drop
        });

        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(true);
          var place = autocomplete.getPlace();
          /*var abc = google.maps.LatLng.getPosition();
          console.log(abc);*/
          /*document.getElementById('loc').value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);*/
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
            map.setZoom(17); 
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindowContent.children['place-icon'].src = place.icon;
          infowindowContent.children['place-name'].textContent = place.name;
          infowindowContent.children['place-address'].textContent = address;
          infowindow.open(map, marker);
        });
  
        var latlng = new google.maps.LatLng(a,b); //Set the default location of map
        var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Place the marker for your location!', //The title on hover to display

        // draggable: true //this makes it drag and drop
        });
            google.maps.event.addListener(marker, 'dragend', function(a) {
            console.log(a);
            document.getElementById('loc').value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4); //Place the value in input box
        });
            

        // setupClickListener('changetype-all', []);
        // setupClickListener('changetype-address', ['address']);
        // setupClickListener('changetype-establishment', ['establishment']);
        // setupClickListener('changetype-geocode', ['geocode']);
      }
      $("form#form1").submit(function(e){
          var classroom = $('#classroom').val();
        if(classroom)
          {
          }
          else{
            e.preventDefault();
            $("#message").html("Please fill all the required fields");
          }
      });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU6bmt7uOJ1WPpcveuiTjdOdf04w1zi_U&libraries=places&callback=initMap&loading=async" async defer></script>

@endsection