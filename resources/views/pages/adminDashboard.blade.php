@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>

    <h2>Users</h2>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                @if (!str_starts_with($user->username, 'deleted')) <!-- Exclude deleted -->
                    <tr>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <a href="{{ route('profile', $user->user_id) }}" class="btn btn-sm btn-primary">View</a>
                            <button type="button" id="deleteAccount" class="btn btn-danger delete-account"
                                data-delete-url="{{ route('adminDeleteAccount', ['id' => $user->user_id]) }}"
                                data-context="admin">
                                Delete
                            </button>
                            @include('partials.blockUserButton', ['user' => $user])
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <h2>Manage Topic Proposals</h2>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->proposal_id }}</td>
                    <td>{{ $proposal->title }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-success accept-proposal" 
                            data-accept-url="{{ route('acceptTopicProposal', ['id' => $proposal->proposal_id]) }}">
                            Accept
                        </button>
                        <button type="button" class="btn btn-sm btn-danger discard-proposal"
                            data-discard-url="{{ route('discardTopicProposal', ['id' => $proposal->proposal_id]) }}">
                            Discard
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
