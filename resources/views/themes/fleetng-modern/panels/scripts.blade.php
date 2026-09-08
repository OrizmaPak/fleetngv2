<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
@yield('vendor-script')
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>
@yield('page-script')
<script src="{{ asset('themes/fleetng-modern/js/app.js') }}?v=1.0.0" defer></script>
