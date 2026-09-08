@extends('layouts/contentLayoutMaster')

@section('title', 'Add Trip')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/croppie.css') }}">
@endsection
<style>
    .error {
        color: red;
    }
</style>

@section('content')
    @php
        use Carbon\Carbon;
        use App\Helpers\Helper;

        $countryCodes = Helper::countryCodes();
    @endphp

    <!-- Ajax Sourced Server-side -->
    <section id="ajax-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Add Trip</h4>
                        <a href="{{ route('trip-list') }}" class="btn btn-outline-dark mr-1"><i
                                data-feather='chevron-left'></i> Back</a>
                    </div>
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
                    <div class="card-body">
                        <form class="form form-horizontal" method="POST" action="{{ route('trip-add') }}"
                            enctype="multipart/form-data" id="tripForm">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <div class="col-12">
                                        <h4>Trip Detail</h4>
                                        <hr>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Pick-up Date & Time*</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <a href="">{{ Carbon::now()->format('d-m-y h:i') }}</a>
                                            </div>
                                            <input type="text" name="pickup_datetime"
                                                value="{{ Carbon::now()->format('Y-m-d H:i') }}" hidden>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Driver<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select name="driver_id" class="form-control" required>
                                                    <option value="">--Select Driver--</option>
                                                    @foreach ($drivers as $driver)
                                                        @if ($driver['is_available'])
                                                            <option value="{{ $driver['id'] }}">{{ $driver['name'] }}
                                                            </option>
                                                        @else
                                                            <option value="{{ $driver['id'] }}" class="text-danger"
                                                                disabled>
                                                                {{ $driver['name'] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('driver_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Pick-up Location<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <select name="pickup_location" class="form-control" required>
                                                    <option value="">--Select Location--</option>
                                                    @foreach ($locations as $location)
                                                        @if ($location['is_active'])
                                                            <option value="{{ $location['id'] }}">
                                                                {{ $location['location_name'] }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('pickup_location')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Drop-off Location<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="text" id="drop_location" class="form-control"
                                                    name="drop_location" placeholder="Enter Drop-off Location"
                                                    value="{{ old('drop_location') }}" required="" />
                                                @error('drop_location')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Trip Cost (in NGN)<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="number" id="trip_cost" class="form-control" name="trip_cost"
                                                    placeholder="Enter Trip Cost (in NGN)" value="{{ old('trip_cost') }}"
                                                    required="" />
                                                @error('trip_cost')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Cost of Sand (in NGN)<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="number" id="cost_of_sand" class="form-control" name="cost_of_sand"
                                                    placeholder="Enter Cost of Sand (in NGN)" value="{{ old('cost_of_sand') }}"
                                                    min="0" max="999999999" />
                                                @error('cost_of_sand')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-sm-3 col-form-label">
                                                <label for="slider_image_head_two">Road Money (in NGN)<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-9">
                                                <input type="number" id="road_money" class="form-control" name="road_money"
                                                    placeholder="Enter Road Money (in NGN)" value="{{ old('road_money') }}"
                                                   min="0" max="999999999" />
                                                @error('road_money')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <h4>Client Detail</h4>
                                        <hr>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-2">
                                            <input type="radio" name="client_type" value="new" id="newClientRadio"
                                                onclick="handleClientType('new')" checked> <label for="newClientRadio">New
                                                Client</label>
                                            <input type="radio" name="client_type" value="existing"
                                                id="existingClientRadio" onclick="handleClientType('existing')"> <label
                                                for="existingClientRadio">Existing</label>
                                        </div>
                                    </div>

                                    <div class="d-none" id="existingClient">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Choose Client<span
                                                            class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <select name="client_id" class="form-control select-existing-client" required>
                                                        <option value="">--Select Client--</option>
                                                        @foreach ($clients as $client)
                                                            <option value="{{ $client['id'] }}">
                                                                {{ $client['phone_number'] }} ({{ $client['full_name'] }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('client_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="" id="newClient">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Full Name<span
                                                            class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" id="client_name" class="form-control"
                                                        name="client_name" placeholder="Full Name"
                                                        value="{{ old('client_name') }}" required="" />
                                                    @error('client_name')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Phone Number<span
                                                            class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <select name="country_code" id="country_code"
                                                                class="form-control">
                                                                @foreach ($countryCodes as $key => $country)
                                                                    <option value="{{ $country['code'] }}"
                                                                        {{ $country['code'] == '+234' ? 'selected' : '' }}>
                                                                        {{ $country['name'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <input type="text" id="client_phone" class="form-control"
                                                                name="client_phone" placeholder="Phone Number"
                                                                value="{{ old('client_phone') }}" maxlength="10" />
                                                            @error('client_phone')
                                                                <div class="error">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3 col-form-label">
                                                    <label for="slider_image_head_two">Email Number<span
                                                            class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="email" id="client_email" class="form-control"
                                                        name="client_email" placeholder="Email"
                                                        value="{{ old('client_email') }}" required />
                                                    @error('client_email')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1 w-25">Submit</button>
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
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>

@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let clientType = 'new';
        $.validator.addMethod('client_phone_required', function(value, element) {
            return clientType == 'new' && !value ? false : true;
        }, "Please enter a clent phone number");

        $.validator.addMethod('customphone', function(value, element) {
            return clientType == 'new' ? this.optional(element) ||
                /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(
                    value) : true;
        }, "Please enter a valid phone number");

        $('#tripForm').validate({
            rules: {
                client_phone: {
                    client_phone_required: true,
                    customphone: true,
                }
            }
        });

        function handleClientType(type) {
            clientType = type;
            if (type == 'new') {
                $('#existingClient').addClass('d-none');
                $('#newClient').removeClass('d-none');
            } else {
                $('#newClient').addClass('d-none');
                $('#existingClient').removeClass('d-none');
            }
        }
        $(document).ready(function() {

            $('.select-existing-client').select2({
                placeholder: '--Select Client--',
                allowClear: false,
            });
        });
    </script>

@endsection
