@extends('layouts.public')
@section('title', $policyTitle)
@section('content')
<section class="public-page-head"><p class="public-eyebrow">Legal</p><h1>{{ $policyTitle }}</h1><p>FleetNG policy information and service terms.</p></section>
<article class="public-policy">{!! $data ? $data->contents : '<p>This policy is currently being updated.</p>' !!}</article>
@endsection
