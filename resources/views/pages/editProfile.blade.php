@extends('layouts.app')

@section('title', "Edit Profile: $user->username")

@section('content')
<div class="container">
    <h1 id="title">Edit {{ $user->username }}'s Profile</h1>

    <div id="success-message" class="alert alert-success" style="display: none;">
        Profile updated successfully!
    </div>

    <div id="error-message" class="alert alert-danger" style="display: none;">
        An error occurred while updating the profile. Please try again.
    </div>


    <form id="editProfileForm">
        @csrf
        @include('partials.editProfileForm', ['user' => $user])

        @if (Auth::user()->isAdmin() && Auth::user()->user_id != $user->user_id && !$user->isAdmin())
            <div class="form-group">
                <label>Make Admin: (coming soon)</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="makeAdmin" disabled>
                </div>
            </div>
        @endif

        <button type="button" id="saveChanges" class="btn btn-primary" 
            data-update-url="{{ Auth::user()->isAdmin() ? route('adminUpdateUser', ['id' => $user->user_id]) : route('updateProfile', ['id' => $user->user_id]) }}">
            Save Changes
        </button>
        <a href="{{ route('profile', ['id' => $user->user_id]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@section('scripts')
@endsection

