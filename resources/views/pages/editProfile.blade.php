@extends('layouts.app')

@section('title', "Edit Profile: $user->username")

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-column align-items-start mb-3">
        <h1 id="title">Edit {{ $user->username }}'s Profile</h1>
        <img 
            id="profile-picture-display" 
            src="{{ asset('storage/' . $user->image->path) }}" 
            alt="Profile Picture" 
            style="max-width: 150px; border: 2px solid #000;"
            class="align-self-center rounded-circle">
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
        <div class="d-flex justify-content-center align-items-start mt-3" style="max-width: 200px; margin: 0 auto;">
            <button type="button" id="saveChanges" style="background-color: #000000; color: white; margin-right: 20px; margin-left: -220px;" 
            data-update-url="{{ Auth::user()->isAdmin() ? route('adminUpdateUser', ['id' => $user->user_id]) : route('updateProfile', ['id' => $user->user_id]) }}">
            Save Changes
            </button>
            <a href="{{ route('profile', ['id' => $user->user_id]) }}" style="background-color: #a9a9a9; color: black; padding: 0.4em; padding-left: 1.5em; padding-right: 1.5em; border-radius: 0.300em;">Cancel</a>
        </div>
    </form>

    <div class="d-flex align-items-center justify-content-center mt-3">
        <button type="button" id="deleteAccount" class="px-5 btn btn-danger delete-account" style="font-weight: 700;"
            data-delete-url="{{ Auth::user()->isAdmin() ? route('adminDeleteAccount', ['id' => $user->user_id]) : route('deleteAccount', ['id' => $user->user_id])  }}">
            Delete Account
        </button>
    </div>
</div>
@endsection
