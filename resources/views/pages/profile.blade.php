@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="profile-container">
    <div class="d-flex justify-content-center">
        <img 
            id="profile-picture-display" 
            src="{{ asset('storage/' . $user->image->path) }}" 
            alt="Profile Picture" 
            class="align-self-center rounded-circle"
            style="max-width: 180px; border: 2px solid #000;">
        </img>
    </div>
    <div class="d-flex justify-content-between">
        <h1>
            {{ $user->username }}
            @if ($currentUser || $canAdminEdit)
                <a href="{{ route('editProfile', ['id' => $user->user_id]) }}" class="ml-2 h1">[edit]</a>
            @endif
        </h1>
        @if ($canAdminEdit)
            @include('partials.blockUserButton', ['user' => $user])
        @endif
    </div>
    <div class="d-flex flex-row justify-content-center align-items-center border-top border-bottom py-4">
        <a href="{{ route('user.posts', ['id' => $user->user_id]) }}" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-solid fa-newspaper fa-7x"></i>
            <span class="position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary" style="font-size: 1.5em;">
            {{ $user->posts->count() }}
            </span>
            <div class="mt-2" style="font-size: 1.2em;">POSTED NEWS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5">
            <i class="fa-regular fa-hashtag fa-7x"></i>
            <div class="mt-2" style="font-size: 1.2em;">FOLLOWED TAGS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5">
            <i class="fa-duotone fa-solid fa-users fa-7x"></i>
            <div class="mt-2" style="font-size: 1.2em;">FOLLOWERS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5">
            <i class="fa-duotone fa-solid fa-users fa-7x"></i>
            <div class="mt-2" style="font-size: 1.2em;">FOLLOWING</div>
        </a>
    </div>


    <p>Reputation: {{ $user->reputation }}</p>
</div>
@endsection
