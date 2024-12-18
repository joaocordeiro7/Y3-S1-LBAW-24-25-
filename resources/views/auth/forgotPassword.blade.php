@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email">Email Address</label>
        <input type="email" name="email" style="font-size:1.4rem; max-width: 500px;" id="email" class="form-control" required>
        @if ($errors->has('email'))
            <span class="error">{{ $errors->first('email') }}</span>
        @endif
        <button type="submit" class="btn btn-primary w-90" style="font-size: 1.3rem;">Send Password Reset Link</button>
    </form>
    @if (session('status'))
        <div class="alert alert-success mt-3">
            {{ session('status') }}
        </div>
    @endif
</div>
@endsection
