@extends('layouts.public')
@section('title', 'Contact Us')
@section('content')
<section class="public-page-head"><p class="public-eyebrow">Contact</p><h1>Talk to FleetNG</h1><p>Send your enquiry to our operations team.</p></section>
<section class="public-contact">
    <div class="public-contact-details"><h2>FleetNG headquarters</h2><address>Suite 6, Scapular Plaza<br>KM 17 Lekki-Epe Expressway<br>Eti-Osa, Lagos, Nigeria</address><a href="tel:+2349064937788">+234 906 493 7788</a><a href="mailto:support@fleetng.com">support@fleetng.com</a></div>
    <form id="public-contact-form" action="{{ route('submit-contact-us') }}" method="POST">
        @csrf
        <div class="public-form-message" data-form-message role="status" hidden></div>
        <label for="contact-name">Name</label><input id="contact-name" name="name" type="text" maxlength="255" required>
        <div class="public-form-row"><div><label for="contact-email">Email</label><input id="contact-email" name="email" type="email" maxlength="255" required></div><div><label for="contact-phone">Phone</label><input id="contact-phone" name="phone" type="tel" placeholder="0801234567" required></div></div>
        <label for="contact-message">Message</label><textarea id="contact-message" name="message" maxlength="1000" rows="6"></textarea>
        <button class="public-button public-button-primary" type="submit">Send message</button>
    </form>
</section>
@endsection
