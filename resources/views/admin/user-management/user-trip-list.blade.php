@extends('layouts/contentLayoutMaster')
@section('title', 'User Trip History')
@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
@endsection
@section('content')
<x-fleetng.page-heading>User Trip History</x-fleetng.page-heading>
<section class="card-datatable">
    <table class="table" id="user-trip-history" aria-label="User trip history">
        <thead><tr><th>Number</th><th>Driver</th><th>Created</th><th>Actions</th></tr></thead>
    </table>
</section>
@endsection
@section('vendor-script')
<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
<script>
$(function () {
    $('#user-trip-history').DataTable({
        processing: true, serverSide: true,
        ajax: @json(route('user-trip-list-detail', $userId)),
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', orderable: false},
            {data: 'trip_generated_at'},
            {data: 'action', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection
