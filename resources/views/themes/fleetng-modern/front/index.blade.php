@extends('layouts.public')
@section('title', 'Your Trusted Logistics Partner')
@section('content')
<section class="public-hero" style="--hero-image: url('{{ asset('front/images/hero-img.png') }}')">
    <div class="public-hero-content">
        <p class="public-eyebrow">Logistics and haulage</p>
        <h1>FleetNG</h1>
        <p>Dependable fleet operations with accountability, visibility and convenience built into every trip.</p>
        <div class="public-actions">
            <a class="public-button public-button-primary" href="{{ config('app.front_url') }}">Request a trip</a>
            <a class="public-button public-button-secondary" href="{{ route('auth-user-login') }}">Open operations portal</a>
        </div>
    </div>
</section>

<section id="services" class="public-band public-services">
    <div class="public-section-heading"><p class="public-eyebrow">What we do</p><h2>Fleet services built for demanding operations</h2></div>
    <div class="public-service-grid">
        <article><img src="{{ asset('front/images/icons/truck.svg') }}" alt=""><h3>Managed haulage</h3><p>Coordinate vehicles, drivers and deliveries from one accountable operation.</p></article>
        <article><img src="{{ asset('front/images/icons/pickup-drop-details.svg') }}" alt=""><h3>Trip visibility</h3><p>Follow trip status, pickup and drop-off activity with clear operational records.</p></article>
        <article><img src="{{ asset('front/images/icons/get-notify.svg') }}" alt=""><h3>Payment updates</h3><p>Keep customers and operations teams informed as trips and payments progress.</p></article>
    </div>
</section>

<section id="about" class="public-band public-about">
    <div><p class="public-eyebrow">Why FleetNG</p><h2>One operational view from request to completion</h2></div>
    <ol class="public-process">
        <li><span>01</span><strong>Request</strong><p>Capture the customer, route and delivery requirements.</p></li>
        <li><span>02</span><strong>Assign</strong><p>Match the trip to an available driver and vehicle.</p></li>
        <li><span>03</span><strong>Track</strong><p>Monitor progress and keep stakeholders informed.</p></li>
        <li><span>04</span><strong>Complete</strong><p>Confirm delivery, payment and operational records.</p></li>
    </ol>
</section>

<section class="public-cta"><div><h2>Ready to move with FleetNG?</h2><p>Request a trip or speak with the operations team.</p></div><a class="public-button public-button-primary" href="{{ url('contact-us') }}">Contact FleetNG</a></section>
@endsection
