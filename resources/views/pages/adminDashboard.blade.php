@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>

    <h2>Users</h2>
    <div class="container">
        <table class="table table-striped table-bordered table-sm custom-table">
            <thead class>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                @if (!str_starts_with($user->username, 'deleted')) 
                    <tr>
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td class=>
                            <a href="{{ route('profile', $user->user_id) }}" class="btn btn-lg mb-2" style="font-size: 1.55rem; font-weight: 700; color: blue; margin-right: 10px;">[view]</a>
                            <button type="button" id="deleteAccount" class="btn delete-account mb-2"
                                data-delete-url="{{ route('adminDeleteAccount', ['id' => $user->user_id]) }}"
                                data-context="admin"
                                style="height:45px; background-color: black; color:white; font-size: 1.25rem; margin-right: 10px;">
                                Delete
                            </button>
                            @include('partials.blockUserButton', ['user' => $user])
                            @if (!$user->isAdmin())
                                <button type="button" class="btn btn-warning promote-user mb-2"
                                    data-promote-url="{{ route('promoteToAdmin', ['id' => $user->user_id]) }}"
                                    style="height:45px; color:black; font-size: 1.25rem; margin-right: 10px;">
                                    Promote to Admin
                                </button>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>

    <h2>Manage Topic Proposals</h2>
    <div class="container">
        <table class="table table-striped table-bordered table-sm custom-table">
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
                        <button type="button" class="btn btn-success mb-2 ms-2 accept-proposal" 
                            data-accept-url="{{ route('acceptTopicProposal', ['id' => $proposal->proposal_id]) }}"
                            style="font-size: 1.25rem; margin-right: 10px;">
                            Accept
                        </button>
                        <button type="button" class="btn btn-danger mb-2 discard-proposal"
                            data-discard-url="{{ route('discardTopicProposal', ['id' => $proposal->proposal_id]) }}"
                            style="font-size: 1.25rem;">
                            Discard
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div>
    <style>
        .form-container {
            border: 2px solid #ccc;
            padding-top: 30px;
            max-width: 350px;
            margin: auto;
        }
        .form-group {
            max-width: 500px;
            margin: auto;
        }
        .form-control {
            font-size: 1.2em;
        }
        label {
            font-size: 1.2em;
        }
    </style>
    <h2 style="margin-left:28px;">Create New User</h2>
    <div id="userCreatedMessage" class="alert alert-success" style="display: none;">
        User created successfully!
    </div>
    <div class="form-container">
        <form id="adminCreateUser" class="d-flex flex-column">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" class="form-control">            
                <span class="error"></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control">
                <span class="error"></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control">
                <span class="error"></span>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                <span class="error"></span>
            </div>
            <div class="align-self-center">
                <button type="button" id="generateUser" data-action-url="{{ route('createUser') }}">Create User</button>
            </div>    
        </form>
    </div>
</div>

@endsection
