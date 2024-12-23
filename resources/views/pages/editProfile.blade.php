@extends('layouts.app')

@section('title', "Edit Profile: $user->username")

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-column align-items-start mb-3">
        <h1 id="title">Edit {{ html_entity_decode($user->username, ENT_QUOTES, 'UTF-8') }}'s Profile</h1>
        <img 
            id="editprofile-picture-display" 
            src="{{ asset('storage/' . $user->image->path) }}" 
            alt="Profile Picture" 
            class="profilepic align-self-center rounded-circle">
        </img>
       

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
        <div class="form-group d-flex me-auto mt-4">
            <button class="black-button m-auto" type="button" id="saveChanges" 
            data-update-url="{{ Auth::user()->isAdmin() ? route('adminUpdateUser', ['id' => $user->user_id]) : route('updateProfile', ['id' => $user->user_id]) }}">
            Save Changes
            </button>
            <button type="button" class="gray-button m-auto" onclick="window.location.href='{{ route('profile', ['id' => $user->user_id]) }}'">Cancel</button>
        </div>
    </form>

    <div class="d-flex align-items-center justify-content-center mt-3">
        <button type="button" id="deleteAccount" class="red-button px-5 btn btn-danger delete-account"
            data-delete-url="{{ Auth::user()->isAdmin() ? route('adminDeleteAccount', ['id' => $user->user_id]) : route('deleteAccount', ['id' => $user->user_id])  }}">
            Delete Account
        </button>
    </div>
</div>
@endsection
