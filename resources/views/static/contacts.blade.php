@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container">
    <h1>Contact Us</h1>
    <p>If you need assistance, have feedback, or want to report an issue, feel free to reach out to us through any of the following methods:</p>
    <div class="card mb-3">
        <div class="card-header">
            Email Support
        </div>
        <div class="card-body">
            <p>You can email us at: <strong><a href="mailto:thebulletin@support.com">thebulletin@support.com</a></strong></p>
            <p>Admins Available Monday to Friday, 9:00 AM - 5:00 PM (WET)</p>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            Feedback Form
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('feedback.submit') }}">
                @csrf
                <div class="form-group" id="contact-us-form">
                    <label for="name">Your Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group" id="contact-us-form">
                    <label for="email">Your Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mw-100" id="contact-us-form">
                    <label for="message">Your Message:</label>
                    <textarea class="form-control h-100 w-100" id="message" name="message" rows="4" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn gray-button mt-3">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
