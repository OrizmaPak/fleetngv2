@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Client')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link href="{{ asset('editor/css/summernote-bs4.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/croppie.css') }}">
@endsection

<style>
    .error {
        color: red;
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
                        <a href="{{ route('client-list') }}" class="btn btn-outline-dark mr-1"><i
                                data-feather='chevron-left'></i> Back</a>
                    </div>

                    {{-- start error msg  --}}
                    @if (Session::get('fail'))
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
                        <form class="form form-horizontal" method="POST"
                            action="{{ route('client-edit', $client->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Full Name*</label>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" id="first_name" class="form-control" name="first_name"
                                                    placeholder="First Name*" value="{{ $client->first_name }}" />
                                                @error('first_name')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="text" id="last_name" class="form-control" name="last_name"
                                                    placeholder="Last Name" value="{{ $client->last_name }}" />
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Phone Number*</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="number" id="phone_number" class="form-control"
                                                    name="phone_number" placeholder="Phone Number"
                                                    value="{{ $client->phone_number }}" required="" />
                                                @error('phone_number')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Email*</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="email" id="email" class="form-control" name="email"
                                                    placeholder="Email ID" value="{{ $client->email }}"
                                                    required="" />
                                                @error('email')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn btn-primary mr-1">Update</button>
                                        <a href="{{ route('client-list') }}" class="btn btn-danger mr-1">Cancel</a>
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
