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
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://maps.google.com/maps/api/js?key={{ config('integrations.google_maps_key') }}&loading=async" async defer></script>
@endsection


@section('content')

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    @if (Session::get('success'))
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
                    @if (Session::get('fail'))
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
                <div class="card">
                    <div class="card-header border-bottom flex-column">
                        <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                            <h4 class="card-title">Map Tracking</h4>
                        </div>
                        <div class="d-flex w-100 justify-content-between flex-wrap flex-md-nowrap align-items-center">
                            <div class="d-flex gap-1">
                                <div class="dropdown user-dropdown">
                                    <button class="btn dropdown-toggle light-blue-btn no-after" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="false">
                                        Vehicle Details <i data-feather="truck"
                                            class="feather feather-truck ml-1 rotate-180"></i>
                                    </button>
                                    <div class="dropdown-menu vehicle-details-box" aria-labelledby="dropdownMenuButton">
                                        <h5 class="dropdown-header">Vehicle Details</h5>
                                        <div class="dropdown-item">
                                            <span>Truck ID:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['registration_no'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Device Sr. No. (IMEI):</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['serial'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Voice Number:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['voice_no'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Billing Term:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['billing_term'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Speed:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['speed'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Driver Name:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['driver_name'] }}</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Last Updated:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ date('d/m/Y H:i:s', strtotime($vehicle_details['last_update'])) }}</label>
                                            </div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Installation Date:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ date('d/m/Y H:i:s', strtotime($vehicle_details['installation_date'])) }}</label>
                                            </div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Distance:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">Today-{{ $vehicle_details['today_distance'] }} KM
                                                    <br>This Week-{{ $vehicle_details['week_distance'] }} KM</label></div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Status:</span>
                                            <div class="drop-detail">
                                                <label class="mb-0">
                                                    @if ($vehicle_details['device_status'] == 0)
                                                        Stopped
                                                    @elseif ($vehicle_details['device_status'] == 1)
                                                        Idling
                                                    @elseif ($vehicle_details['device_status'] == 2)
                                                        Moving
                                                    @elseif ($vehicle_details['device_status'] == 4)
                                                        Unreachable
                                                    @else
                                                        Inactive
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        <div class="dropdown-item">
                                            <span>Location:</span>
                                            <div class="drop-detail"><label
                                                    class="mb-0">{{ $vehicle_details['arial_distance'] .
                                                        " KM from
                                                                            " .
                                                        $vehicle_details['place'] }}
                                                </label></div>
                                        </div>
                                        <div class="border-top mt-1 text-center">
                                            <button class="btn"><i data-feather="refresh-cw"
                                                    class="feather feather-refresh-cw color-blue"></i></button>
                                        </div>

                                    </div>
                                </div>
                                <div>
                                    <button class="btn light-blue-btn" id="fullScreen">Full Screen <i
                                            data-feather="maximize" class="feather feather-maximize ml-1"></i></button>
                                </div>
                            </div>
                            <div class="mt-2 mt-md-0 mb-2 mb-md-0">
                                <div class="d-flex gap-1">
                                    <select name="" id="" class="form-control custom-form-control">
                                        <option value="" selected>Choose Status </option>
                                        <option value="2">Moving</option>
                                        <option value="0">Stopped</option>
                                        <option value="1">Idle</option>
                                        <option value="4">Unreachable</option>
                                        <option value="">Alert</option>
                                    </select>
                                    <select name="" id="" class="form-control custom-form-control">
                                        <option value="" selected>Select Action</option>
                                        <option value="">Reset</option>
                                        <option value="">Start</option>
                                        <option value="">Stop</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="map-canvas" style="height: 500px"></div>
                {{-- <div class="live-map-div">
        <iframe
          src="{{ 'https://www.google.com/maps?q='.$vehicle_details['lat'].','.$vehicle_details['long'].'&z=15&output=embed' }}"
          width="100%" height="600" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div> --}}
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
        $('#fullScreen').click(function(e) {
            var map = document.querySelector("#map-canvas");
            map.requestFullscreen();
        });
        var lat = "{{ $vehicle_details['lat'] }}";
        var long = "{{ $vehicle_details['long'] }}";

        function getIcon(device_status) {
            if (device_status == 0)
                var icon =  {
                    url: "{{ asset('map-icons/red.png') }}",
                    scaledSize: new google.maps.Size(40, 40)
                };
            else if (device_status == 1)
                var icon =  {
                    url: "{{ asset('map-icons/yellow.png') }}",
                    scaledSize: new google.maps.Size(40, 40)
                };
            else if (device_status == 2)
                var icon = {
                    url: "{{ asset('map-icons/green.png') }}",
                    scaledSize: new google.maps.Size(40, 40)
                };
            else if (device_status == 4)
                var icon =  {
                    url: "{{ asset('map-icons/black.png') }}",
                    scaledSize: new google.maps.Size(40, 40)
                };
            else
                var icon =  {
                    url: "{{ asset('map-icons/grey.png') }}",
                    scaledSize: new google.maps.Size(40, 40)
                };
            return icon;
        }

        var device_status = {{ $vehicle_details['device_status'] }};
        var icon = getIcon(device_status);
        var myLatlng = new google.maps.LatLng(lat, long);

        function initialize() {
            var mapOptions = {
                zoom: 18,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.TERRAIN
            };

            var map = new google.maps.Map(document.getElementById('map-canvas'),
                mapOptions);

            var lat_long = ['{{ $lat_long }}'];

            if (device_status == 2) {

                var r = lat_long;
                var coordinates = r[0].split("|");
                var flightPlanCoordinates = new Array();
                var bounds = new google.maps.LatLngBounds();
                for (i = 0; i < coordinates.length; i++) {

                    if (i == coordinates.length - 1) {
                        marker = new google.maps.Marker({
                            icon: icon,
                            position: new google.maps.LatLng(coordinates[i].split(',')[0], coordinates[i].split(
                                ',')[1]),
                            map: map,
                            optimized: false
                        });

                        map.setZoom(18);
                        map.panTo(marker.position);

                        interval = setInterval(function() {
                            toggleMarker()
                        }, 500);

                        function toggleMarker() {
                            if (marker.getVisible()) {
                                marker.setVisible(false);
                            } else {
                                marker.setVisible(true);
                            }
                        }
                    }
                    var point = new google.maps.LatLng(coordinates[i].split(',')[0], coordinates[i].split(',')[1]);
                    bounds.extend(point);
                    flightPlanCoordinates.push(point);
                }


                var flightPath = new google.maps.Polyline({
                    path: flightPlanCoordinates,
                    geodesic: true,
                    strokeColor: '#008000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                flightPath.setMap(map);
                map.fitBounds(bounds);
            } else {

                var mapOptions = {
                    zoom: 18,
                    center: myLatlng,
                    mapTypeId: google.maps.MapTypeId.TERRAIN,
                }
                var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
                    animation: google.maps.Animation.DROP,
                    icon: icon
                });

                interval = setInterval(function() {
                    toggleMarker()
                }, 500);

                function toggleMarker() {
                    if (marker.getVisible()) {
                        marker.setVisible(false);
                    } else {
                        marker.setVisible(true);
                    }
                }

            }
            const infowindow = new google.maps.InfoWindow({
              content: "<b>TRUCK ID:</b> {{$vehicle_details['registration_no']}}",
              ariaLabel: "TRUCK",
            });

            infowindow.open({
              anchor: marker,
              map,
            });
            
            marker.addListener("click", () => {
              infowindow.open({
                  anchor: marker,
                  map,
                });
            });
        }

        window.addEventListener('load', initialize);

        setInterval(ajax_query, 15000); //time in milliseconds 

        function ajax_query() {
            //Call ajax here
            $.ajax({
                type: "POST",
                url: "{{ route('map-tracking', $vehicle_details['device_id']) }}",
                dataType: "json",
                data: {},
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr(
                        'content'));
                },
                success: function(response) {
                    //alert(JSON.stringify(response));


                    if (response.message == "Data Found") {


                        var lat = response.vehicle_details.lat;
                        var long = response.vehicle_details.long;


                        var device_status = response.vehicle_details.device_status;
                        var icon = getIcon(device_status);

                        var myLatlng = new google.maps.LatLng(lat, long);

                        var mapOptions = {
                            zoom: 18,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.TERRAIN
                        };

                        var map = new google.maps.Map(document.getElementById('map-canvas'),
                            mapOptions);


                        var lat_long = [response.lat_long];

                        if (device_status == 2) {

                            var r = lat_long;
                            var coordinates = r[0].split("|");
                            var flightPlanCoordinates = new Array();
                            var bounds = new google.maps.LatLngBounds();
                            for (i = 0; i < coordinates.length; i++) {

                                if (i == coordinates.length - 1) {
                                    marker = new google.maps.Marker({
                                        icon: icon,
                                        position: new google.maps.LatLng(lat, long),
                                        map: map,
                                        optimized: false
                                    });

                                    // put marker on map
                                    marker.setMap(map);

                                    // center on marker
                                    map.setCenter(new google.maps.LatLng(lat, long));

                                    map.setZoom(18);
                                    map.panTo(marker.position);

                                    interval = setInterval(function() {
                                        toggleMarker()
                                    }, 500);

                                    function toggleMarker() {
                                        if (marker.getVisible()) {
                                            marker.setVisible(false);
                                        } else {
                                            marker.setVisible(true);
                                        }
                                    }
                                }
                                var point = new google.maps.LatLng(coordinates[i].split(',')[0], coordinates[i]
                                    .split(',')[1]);
                                bounds.extend(point);
                                flightPlanCoordinates.push(point);
                            }


                            var flightPath = new google.maps.Polyline({
                                path: flightPlanCoordinates,
                                geodesic: true,
                                strokeColor: '#008000',
                                strokeOpacity: 1.0,
                                strokeWeight: 2
                            });

                            flightPath.setMap(map);
                            map.fitBounds(bounds);
                        } else {

                            var mapOptions = {
                                zoom: 18,
                                center: myLatlng,
                                mapTypeId: google.maps.MapTypeId.TERRAIN,
                            }
                            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                            var marker = new google.maps.Marker({
                                position: myLatlng,
                                map: map,
                                icon: icon
                            });

                            interval = setInterval(function() {
                                toggleMarker()
                            }, 500);

                            function toggleMarker() {
                                if (marker.getVisible()) {
                                    marker.setVisible(false);
                                } else {
                                    marker.setVisible(true);
                                }
                            }

                        }

                        const infowindow = new google.maps.InfoWindow({
                          content: "<b>TRUCK ID:</b> {{$vehicle_details['registration_no']}}",
                          ariaLabel: "TRUCK",
                        });
                        infowindow.open({
                            anchor: marker,
                            map,
                          });
                        
                        marker.addListener("click", () => {
                          infowindow.open({
                              anchor: marker,
                              map,
                            });
                        });


                    } else if (response.message == "No record found") {
                        $('#error_alert').css('display', 'block').html(response.message);
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
@endsection
