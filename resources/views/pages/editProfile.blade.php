@extends('layouts.app')

@section('title', "Edit Profile: $user->username")

@section('content')
<div class="container">
    <h1>Edit {{ $user->username }}'s Profile</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ Auth::user()->isAdmin() ? route('adminUpdateUser', ['id' => $user->user_id]) : route('updateProfile', ['id' => $user->user_id]) }}" method="POST">
        @csrf

        @include('partials.editProfileForm', ['user' => $user])

        @if (Auth::user()->isAdmin())
            <div class="form-group">
                <label for="admin_note">Admin Note:</label>
                <textarea name="admin_note" id="admin_note" class="form-control">{{ old('admin_note', $user->admin_note ?? '') }}</textarea>
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('profile', ['id' => $user->user_id]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
