@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" class="form-control" required>
        @if ($errors->has('email'))
            <span class="error">{{ $errors->first('email') }}</span>
        @endif
        <button type="submit" class="btn btn-primary mt-2">Send Password Reset Link</button>
    </form>
    @if (session('status'))
        <div class="alert alert-success mt-3">
            {{ session('status') }}
        </div>
    @endif
</div>
@endsection
