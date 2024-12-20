@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reset Password</h2>
    <form method="POST" action="{{ $updateRoute }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <label for="password">New Password</label>
        <input type="password" name="password" id="password" class="form-control w-25" required>
        @if ($errors->has('password'))
            <span class="error">{{ $errors->first('password') }}</span>
        @endif

        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control w-25" required>

        <button type="submit" class="blue-button btn mt-2">Reset Password</button>
    </form>
</div>
@endsection

