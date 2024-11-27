@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="profile-container">
    <h1>{{ $user->username }}'s Profile</h1>
    <p>Email: {{ $user->email }}</p>
    <p>Reputation: {{ $user->reputation }}</p>

    @if ($currentUser)
        <a href="{{ route('editProfile', ['id' => $user->user_id]) }}" class="btn btn-primary">Edit Profile</a>
    @elseif ($canAdminEdit)
        <a href="{{ route('editProfile', ['id' => $user->user_id]) }}" class="btn btn-primary">Edit as Admin</a>
    @endif
    <a href="{{ route('user.posts', ['id' => $user->user_id]) }}" class="btn btn-primary">View All Posts</a>
</div>
@endsection
