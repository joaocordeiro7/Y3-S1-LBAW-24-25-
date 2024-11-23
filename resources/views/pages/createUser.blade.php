@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="container">
    <h1>Create New User</h1>
    <form method="POST" action="{{ route('adminCreateUser') }}">
        @csrf

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Create User</button>
        <a href="{{ route('adminDashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
