@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container">
    <h1>Edit Profile</h1>
    <form method="POST" action="{{ route('updateProfile', ['id' => $user->user_id]) }}">
        @csrf

        <!-- Username -->
        <div class="form-group">
            <label for="username">Username</label>
            <input 
                id="username" 
                type="text" 
                name="username" 
                value="{{ old('username', $user->username) }}" 
                class="form-control @error('username') is-invalid @enderror">
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email', $user->email) }}" 
                class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">New Password</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('profile', ['id' => $user->user_id]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
