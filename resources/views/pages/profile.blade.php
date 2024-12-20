@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="profile-container">
    <div class="d-flex justify-content-center">
        <img 
            id="profile-picture-display" 
            src="{{ asset('storage/' . $user->image->path) }}" 
            alt="Profile Picture" 
            class="profilepic align-self-center rounded-circle">
        </img>
    </div>
    <div class="d-flex justify-content-between">
        <h1>
            {{ $user->username }}
            @if ($currentUser || $canAdminEdit)
                <a href="{{ route('editProfile', ['id' => $user->user_id]) }}" class="ml-2 h1">[edit]</a>
            @endif
        </h1>
        @if(!$currentUser && Auth::user()->alreadyFollows($user->user_id))
            <button class="unfollow btn btn-lg p-4" 
            data-id="{{$user->user_id}}">unFollow</button>
        @endif
        @if (!$currentUser && !Auth::user()->alreadyFollows($user->user_id))
            <button class="follow btn btn-lg p-4"
            data-id="{{$user->user_id}}">Follow</button>
        @endif
    </div>
    <div class="d-flex flex-row justify-content-center align-items-center border-top border-bottom py-4">
        <a href="{{ route('user.posts', ['id' => $user->user_id]) }}" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-solid fa-newspaper fa-7x"></i>
            <span class="icon position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary">
            {{ $user->posts->count() }}
            </span>
            <div class="icon-text-under mt-3">POSTED NEWS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-regular fa-hashtag fa-7x"></i>
            <div class="icon-text-under mt-3">FOLLOWED TAGS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-duotone fa-solid fa-users fa-7x"></i>
            <span class="icon position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary">
            {{ $user->followedBy()->count() }}
            </span>
            <div class="icon-text-under mt-3">FOLLOWERS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-duotone fa-solid fa-users fa-7x"></i>
            <span class="icon position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary">
            {{ $user->follows()->count() }}
            </span>
            <div class="icon-text-under mt-3">FOLLOWING</div>
        </a>
    </div>


    <p>Reputation: {{ $user->reputation }}</p>

    @if ($canAdminEdit)
            @include('partials.blockUserButton', ['user' => $user])
    @endif

</div>
@endsection
