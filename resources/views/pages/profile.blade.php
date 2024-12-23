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
            {{ html_entity_decode($user->username, ENT_QUOTES, 'UTF-8') }}
            @if ($currentUser || $canAdminEdit)
                <a href="{{ route('editProfile', ['id' => $user->user_id]) }}" class="ml-2 h1">[edit]</a>
            @endif
        </h1>
        @if (Auth::check())
            @if(!$currentUser && Auth::user()->alreadyFollows($user->user_id))
            <button class="unfollow btn btn-lg p-4" 
            data-id="{{$user->user_id}}">unFollow</button>
            @endif
            @if (!$currentUser && !Auth::user()->alreadyFollows($user->user_id))
            <button class="follow btn btn-lg p-4"
            data-id="{{$user->user_id}}">Follow</button>
            @endif
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
            <i class="fa-regular fa-hashtag fa-7x" onclick="openFollowedTagsList()"></i>
            <span class="position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary" style="font-size: 1.5em;">
            {{ $user->followedTags()->count() }}
            </span>
            <div class="icon-text-under mt-3">FOLLOWED TAGS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-duotone fa-solid fa-users fa-7x" onclick="openFollowersList()"></i>
            <span class="position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary" style="font-size: 1.5em;">
            {{ $user->followedBy()->count() }}
            </span>
            <div class="icon-text-under mt-3">FOLLOWERS</div>
        </a>

        <a href="#" class="btn w-200 text-center mx-5 position-relative">
            <i class="fa-duotone fa-solid fa-users fa-7x" onclick="openFollowingList()"></i>
            <span class="position-absolute top-0 start-10 translate-middle-x badge rounded-pill bg-primary" style="font-size: 1.5em;">
            {{ $user->follows()->count() }}
            </span>
            <div class="icon-text-under mt-3">FOLLOWING</div>
        </a>
    </div>
    <!-- Followed List -->
    <script>
        const userId = {{ $user->user_id }};
    </script>
    <div id="followersModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050;">
        <div style="margin: 10% auto; background: white; padding: 20px; max-width: 400px; border-radius: 8px;">
            <h4 class="mb-4">Users following you</h4>
            <ul id="followersList"  style="list-style-type: none; padding: 0; margin: 0;"></ul>  
            <div class="d-flex justify-content-end gap-4">
                <button type="button" class="btn btn-secondary" onclick="closeFollowersList()" style="font-size:1.2rem;">Go Back</button>
            </div>
        </div>
    </div>
    <div id="followingModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050;">
        <div style="margin: 10% auto; background: white; padding: 20px; max-width: 400px; border-radius: 8px;">
            <h4 class="mb-4">Users you follow</h4>
            <ul id="followingList"  style="list-style-type: none; padding: 0; margin: 0;"></ul>  
            <div class="d-flex justify-content-end gap-4">
                <button type="button" class="btn btn-secondary" onclick="closeFollowingList()" style="font-size:1.2rem;">Go Back</button>
            </div>
        </div>
    </div>
    <div id="followedTagsModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050;">
        <div style="margin: 10% auto; background: white; padding: 20px; max-width: 400px; border-radius: 8px;">
            <h4 class="mb-4">Tags you follow</h4>
            <ul id="followedTagsList"  style="list-style-type: none; padding: 0; margin: 0;"></ul>  
            <div class="d-flex justify-content-end gap-4">
                <button type="button" class="btn btn-secondary" onclick="closeFollowedTagsList()" style="font-size:1.2rem;">Go Back</button>
            </div>
        </div>
    </div>

    <p>Reputation: {{ $user->reputation }}</p>
    <div class="vote-bar">
        <div class="upvotes" style="flex: {{ $upvotes }};">
            {{ $upvotes }}
        </div>
        <div class="downvotes" style="flex: {{ $downvotes }};">
            {{ $downvotes }}
        </div>
    </div>


    @if ($canAdminEdit)
            @include('partials.blockUserButton', ['user' => $user])
    @endif

</div>
@endsection
