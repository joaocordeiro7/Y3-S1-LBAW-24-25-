@extends('layouts.app')

@section('title', "Edit Profile: $user->username")

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1 id="title">Edit {{ $user->username }}'s Profile</h1>
        <img 
            id="profile-picture-display" 
            src="{{ asset('storage/' . $user->image->path) }}" 
            alt="Profile Picture" 
            style="max-width: 150px;">
        </img>
        <button type="button" id="deleteAccount" class="btn btn-danger delete-account" 
            data-delete-url="{{ Auth::user()->isAdmin() ? route('adminDeleteAccount', ['id' => $user->user_id]) : route('deleteAccount', ['id' => $user->user_id])  }}">
            Delete Account
        </button>

    </div>

    <div id="success-message" class="alert alert-success" style="display: none;">
        Profile updated successfully!
    </div>

    <div id="error-message" class="alert alert-danger" style="display: none;">
        An error occurred while updating the profile. Please try again.
    </div>

    <form id="editProfileForm" enctype="multipart/form-data">
        {{ csrf_field() }}
        @include('partials.editProfileForm', ['user' => $user])

        <button type="button" id="saveChanges" class="btn btn-primary" 
            data-update-url="{{ Auth::user()->isAdmin() ? route('adminUpdateUser', ['id' => $user->user_id]) : route('updateProfile', ['id' => $user->user_id]) }}">
            Save Changes
        </button>
        <a href="{{ route('profile', ['id' => $user->user_id]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
