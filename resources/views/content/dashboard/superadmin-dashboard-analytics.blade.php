
@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytics')

@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap.min.css')) }}">
@endsection
@section('page-style')
  <!-- Page css files -->
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-invoice-list.css')) }}">
  @endsection

@section('content')
<!-- Dashboard Analytics Start -->
<section id="dashboard-analytics">
  
  <div class="row match-height">

    <!-- Statistics Card -->
    <div class="col-xl-12 col-md-6 col-12">


     

      <div class="card card-statistics">
        <div class="card-header">
          <h4 class="card-title">Statistics</h4>
          <div class="d-flex align-items-center">
            <!-- <p class="card-text font-small-2 mr-25 mb-0">Updated just now</p> -->
          </div>
        </div>
        <div class="card-body statistics-body">
          @if (session('user_role') == 1)
              <div class="row mb-2">
                <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                  <a href="{{route('user-list')}}">
                    <div class="media">
                      <div class="avatar bg-light-primary mr-2">
                          <div class="avatar-content">
                            <i data-feather="user" class="avatar-icon"></i>
                          </div>
                      </div>
                      <div class="media-body my-auto">
                        <h4 class="font-weight-bolder mb-0">{{$total_users}}</h4>
                        <p class="card-text font-small-3 mb-0">Total Users</p>
                      </div>
                    </div>
                  </a>                
                </div>
                <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
                  <a href="{{route('user-list')}}">
                    <div class="media">
                      <div class="avatar bg-light-info mr-2">
                          <div class="avatar-content">
                            <i data-feather="user" class="avatar-icon"></i>
                          </div>
                      </div>
                      <div class="media-body my-auto">
                        <h4 class="font-weight-bolder mb-0">{{$total_active_users}}</h4>
                        <p class="card-text font-small-3 mb-0">Total Active Users</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                  <a href="{{route('user-list')}}">
                    <div class="media">
                      <div class="avatar bg-light-success mr-2">
                          <div class="avatar-content">
                            <i data-feather="user" class="avatar-icon"></i>
                          </div>
                      </div>
                      <div class="media-body my-auto">
                        <h4 class="font-weight-bolder mb-0">{{$total_inactive_users}}</h4>
                        <p class="card-text font-small-3 mb-0">Total Inactive Users</p>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <hr>
          @endif
          <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
              <a href="{{route('merchant-list')}}">
                <div class="media">
                  <div class="avatar bg-light-primary mr-2">
                      <div class="avatar-content">
                        <i data-feather="user" class="avatar-icon"></i>
                      </div>
                  </div>
                  <div class="media-body my-auto">
                    <h4 class="font-weight-bolder mb-0">{{$total_merchants}}</h4>
                    <p class="card-text font-small-3 mb-0">Total Merchants</p>
                  </div>
                </div>
              </a>                
            </div>
             <div class="col-xl-3 col-sm-6 col-12 mb-2 mb-xl-0">
              <a href="{{route('merchant-list')}}">
                <div class="media">
                  <div class="avatar bg-light-info mr-2">
                      <div class="avatar-content">
                        <i data-feather="user" class="avatar-icon"></i>
                      </div>
                  </div>
                  <div class="media-body my-auto">
                    <h4 class="font-weight-bolder mb-0">{{$total_active_merchants}}</h4>
                    <p class="card-text font-small-3 mb-0">Total Active Merchants</p>
                  </div>
                </div>
              </a>
            </div> 
         
           <div class="col-xl-3 col-sm-6 col-12">
              <a href="{{route('merchant-list')}}">
                <div class="media">
                  <div class="avatar bg-light-success mr-2">
                      <div class="avatar-content">
                        <i data-feather="user" class="avatar-icon"></i>
                      </div>
                  </div>
                  <div class="media-body my-auto">
                    <h4 class="font-weight-bolder mb-0">{{$total_inactive_merchants}}</h4>
                    <p class="card-text font-small-3 mb-0">Total Inactive Merchants</p>
                  </div>
                </div>
              </a>
              
            </div> 
          </div>
        </div>
      </div>


    
    </div>
    <!--/ Statistics Card -->
  </div>
  


</section>
<!-- Dashboard Analytics end -->
@endsection

@section('vendor-script')
  <!-- vendor files -->
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap.min.js')) }}"></script>
@endsection
@section('page-script')
  <!-- Page js files -->
  <script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
  <script src="{{ asset(mix('js/scripts/pages/app-invoice-list.js')) }}"></script>
@endsection
