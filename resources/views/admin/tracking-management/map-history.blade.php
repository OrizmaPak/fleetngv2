
@extends('layouts/contentLayoutMaster')

@section('title', 'Tracking Management')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" type="text/css" href="{{asset('css/base/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection


@section('content')

<style>
html,
body,
#map-canvas {
  height: 100%;
  width: 100%;
  margin: 0px;
  padding: 0px
}
ul.typeahead.dropdown-menu{
  z-index:999;
}
</style>
<script src="https://maps.google.com/maps/api/js?key={{ config('integrations.google_maps_key') }}&loading=async"></script>

<!-- Ajax Sourced Server-side -->
<section id="ajax-datatable">
  <div class="container">
      <div class="row">
          <div class="col-sm-12">
              @if(Session::get('success'))
              <div class="demo-spacing-0">
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                  <div class="alert-body">
                    {{ Session::get('success') }}
                  </div>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              </div>
              @endif
              @if(!empty(Session::get('fail')))
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
          </div>
      </div>
  </div> 
  <div class="row">
    <div class="col-12">
    <p class="alert-danger text-center p-1" style="display:none;" id="error_alert"></p>

      <div class="card">
        <div class="card-header border-bottom flex-column">
          <div class="d-flex align-items-center justify-content-between w-100 mb-1">
            <h4 class="card-title">Map History</h4> 
            <div class="btn-toolbar mb-2 mb-md-0">
              <div class="me-2 print-options-btn">                
                <button type="button" class="btn btn-sm btn-outline-secondary btn-warning">Idling</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-success">Moving</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-danger">Stopped</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-dark">Unreachable</button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-secondary">Inactive</button>
              </div>
            </div>
          </div>

          <div class="d-flex w-100 gap-1 align-items-center justify-content-between flex-wrap flex-md-nowrap align-items-center searching-div mb-1">
                <p class="flex-shrink-0 mb-0">Make a search</p>
                {{-- <div class="flex-shrink-0">
                  <div class="vehicle-dropdown">
                      <button onclick="showVehicles()" class="dropbtn">Choose a Vehicle</button>
                      <div id="vehicleDropdown" class="dropdown-content">
                        <input type="text" placeholder="Search.." id="searchInput" class="w-100" onkeyup="filterFunction()">
                      @if(isset($vehicle_details))
                     @foreach($vehicle_details as $vehicle)
                      <a href="#" class="nav-link"> {{ $vehicle['registration_no'] }}</a>
                      @endforeach
                      @endif
                      </div>
                    </div>
                </div> --}}
                <div class="d-flex flex-1 gap-1">
                    <input type="text" data-provide="typeahead" id="vehical_id" name="search" class="typeahead form-control br-25"  placeholder="Truck ID">
                </div>
                <div class="d-flex flex-1 gap-1">
                    <input type="text" id="date_range" class="form-control flatpickr-range br-25" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                </div>
                <div class="flex-shrink-0">
                    <button class="btn div-btn blue" id="search" onclick="search()"><i data-feather="search" class="feather feather-search"></i> &nbsp; Search</button>
                </div>
                <div class="flex-shrink-0">
                    <button class="btn div-btn green" id="reset" onclick="reset()"><img src="{{asset('images/icons/reset-white.svg')}}" class="img-fluid" alt="Reset Fields"> Reset</button>
                </div>
            </div>

            <div class="w-100 d-flex gap-1 align-items-md-center justify-content-between flex-wrap flex-md-nowrap align-items-center playing-div">
                <p class="flex-shrink-0 mb-0">Total Distance: <span class="medium-fw" id="total_distance"></span></p>

                {{-- <div class="d-flex gap-less">
                    <select name="" id="" class="form-control custom-form-control bg-transparent">
                        <option value="" selected>Play Speed</option>
                        <option value="">1x</option>
                        <option value="">2x</option>
                        <option value="">3x</option>
                        <option value="">5x</option>
                        <option value="">6x</option>
                        <option value="">8x</option>
                    </select>
                    <div>
                        <button class="btn play-pause-btn">
                            <img src="{{asset('images/icons/play.svg')}}" class="img-fluid play-icon" alt="Play">
                            <img src="{{asset('images/icons/pause.svg')}}" class="img-fluid pause-icon" alt="Pause">
                        </button>
                    </div>
                </div> --}}
            </div>
            <div id="userList_processing" class="dataTables_processing card mt-2" style="display: none;">Processing...</div>
        </div>
      </div>
    <div style="position:relative"><div id="map-canvas" style="height: 500px"></div>
    <div class="search-details-div">
      <button class="btn collapsed" data-toggle="collapse" href="#searchDiv" role="button" aria-expanded="false" aria-controls="searchDiv">Search Details</button>
      <div class="search-details-inner">
          <div class="collapse multi-collapse" id="searchDiv">
              <div class="dropdown-item">
                  <span>Truck ID:</span>
                  <div class="drop-detail"><label class="mb-0" id="truck_id"></label></div>
              </div>
               <div class="dropdown-item">
                  <span>Device Sr. No. (IMEI):</span>
                  <div class="drop-detail"><label class="mb-0" id="device_serial"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>Voice Number:</span>
                  <div class="drop-detail"><label class="mb-0" id="voice_number"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>Billing Term:</span>
                  <div class="drop-detail"><label class="mb-0" id="billing_term"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>Speed:</span>
                  <div class="drop-detail"><label class="mb-0" id="speed"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>From:</span>
                  <div class="drop-detail"><label class="mb-0" id="from_date"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>To:</span>
                  <div class="drop-detail"><label class="mb-0" id="to_date"></label></div>
              </div>
              <div class="dropdown-item">
                  <span>Location:</span>
                  <div class="drop-detail"><label class="mb-0" id="location"></label></div>
              </div>
          </div>
      </div>
  </div>
</div>

      <div class="live-map-div"> 
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

  {{-- date range picker --}}
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
  <script>
    $('#fullScreen').click(function(e){
            var map = document.querySelector(".live-map-div iframe");
            map.requestFullscreen();
        });

      $(function(){
        $("#toggleBtn").on("click", function(){
          $(".sidebar-div").toggleClass("shrink");
          $(".mob-overlay").addClass("show");
        });

        $(".mob-overlay").on("click", function(){
          $(".sidebar-div").removeClass("shrink");
          $(this).removeClass("show");
        });

        $('#fullScreen').click(function(e){
            var map = document.querySelector(".live-map-div iframe");
            map.requestFullscreen();
        });

        $('.dateRangePicker').daterangepicker({
        autoUpdateInput: false,
          locale: {
            format: 'MM/DD/YYYY',
            cancelLabel: 'Clear'
          }
      });

      $('.dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
      });

      $('.dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });
      });


      function showVehicles() {
        document.getElementById("vehicleDropdown").classList.toggle("show");
    }

    function filterFunction() {
        var input, filter, ul, li, a, i;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        div = document.getElementById("vehicleDropdown");
        a = div.getElementsByTagName("a");

        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
            } else {
            a[i].style.display = "none";
            }
        }
    }


    $(".play-pause-btn").on("click", function(){
        $(this).toggleClass("play");
    });

  $(".search-details-div").hide();

  //reset search form
  function reset()
        {
          $('#vehicleDropdown').val('');
          $('#vehical_id').val('');
          $('.dropbtn').text('Choose a Vehicle');
          $('#date_range').val('');
        }

  //search form start
  function search()
        {
            var search = $('#vehical_id').val();
            var date_range  = $('#date_range').val(); 
            
          
            $('#error_alert').css('display','none');
            
            
            if(search == "Choose a Vehicle")
            {
              $('#error_alert').css('display','block').html('Please provide a valid vehicle id.');
              $('#dropbtn').focus();
            }            
            else
            {
              if(!search && !date_range)
              {
              $('#error_alert').css('display','block').html('Please provide at least one field.');
                return false;
              }
              
              if(date_range != '') {
                date_range=date_range.split('to').length == 1 ? date_range+' to '+date_range:date_range;
              }
              $('#userList_processing').show();
               $.ajax({
                type: "POST",
                url: "{{ route("map-history") }}",
                data: {
                    'search': search,
                    'date_range': date_range          
                },
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                $('#userList_processing').hide();
                    if(response.message == "Distance Found")
                    { 
                        var mapOptions = {
                          zoom: 3,
                          center: new google.maps.LatLng(0, -180),
                          mapTypeId: google.maps.MapTypeId.TERRAIN
                        };

                        var map = new google.maps.Map(document.getElementById('map-canvas'),
                            mapOptions);

                      var r=[response.lat_long];
                      var coordinates = r[0].split("|");
                      var flightPlanCoordinates = new Array();
                      var bounds = new google.maps.LatLngBounds();
                      for(i=0;i<coordinates.length;i++)
                      {  
                        if(i==0)
                        {
                        
                        var icon = new google.maps.MarkerImage('https://maps.google.com/mapfiles/ms/micons/green.png');                       
                      }
                      else if(i==coordinates.length-1)
                      {
                        var icon = new google.maps.MarkerImage('https://maps.google.com/mapfiles/ms/micons/red.png');
                      }
                      if(i==0 || i==coordinates.length-1 )
                      {
                      marker = new google.maps.Marker({
                          icon: icon,
                          position: new google.maps.LatLng(coordinates[i].split(',')[0],coordinates[i].split(',')[1]),
                          map: map
                        });
                      }

                        var point =new google.maps.LatLng(coordinates[i].split(',')[0],coordinates[i].split(',')[1]);
                        bounds.extend(point);
                        flightPlanCoordinates.push(point);   
                      }   


                      var flightPath = new google.maps.Polyline({
                      path: flightPlanCoordinates,
                      geodesic: true,
                      strokeColor: '#FF0000',
                      strokeOpacity: 1.0,
                      strokeWeight: 2
                      });

                      flightPath.setMap(map);
                      map.fitBounds(bounds);

                      $('#total_distance').text(response.data.vehicledistance_data[0].device_data[0].total_distance);

                      $(".search-details-div").show();                      
                      $('#truck_id').text(response.vehicle_details.registration_no);
                      $('#device_serial').text(response.vehicle_details.serial);
                      $('#voice_number').text(response.vehicle_details.voice_no);
                      $('#billing_term').text(response.vehicle_details.billing_term);
                      $('#speed').text(response.vehicle_details.speed);
                      $('#from_date').text(response.from_date);
                      $('#to_date').text(response.to_date);
                      var location=response.aerial_distance+" KM from "+response.place;
                      $('#location').text(location);
                      
                      
                    }
                    else if(response.message == "No record found")
                    {                                        
                      hideMap();
                      $('#error_alert').css('display','block').html(response.message);
                    }
                    else if(response.message == "Please select only 3 months before date.")
                    {                                        
                      hideMap();
                      $('#error_alert').css('display','block').html(response.message);
                    }
                    else
                    {
                      hideMap();
                      console.log('error in activity ajax call.');
                    }
                },
                error: function (error) {
                    hideMap();
                    console.error(error);
                    $('#error_alert').css('display','block').html('Something went wrong!.');
                    $("#alert_msg1").html('Failed! Please try again.');
                }
            });
            }            
        }

        function hideMap() {
          $('#total_distance').html('');
          $('#map-canvas').html('');
          $('.search-details-div').hide();
        }
    //search form end
    </script>
@endsection
