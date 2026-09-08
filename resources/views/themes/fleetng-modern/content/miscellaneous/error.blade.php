@extends('layouts/fullLayoutMaster')
@section('title', 'Page Not Found')
@section('content')
@include('content.miscellaneous._state', ['icon' => 'map-pin', 'eyebrow' => 'Error 404', 'heading' => 'Page not found', 'message' => 'The page may have moved or the address may be incorrect.'])
@endsection
