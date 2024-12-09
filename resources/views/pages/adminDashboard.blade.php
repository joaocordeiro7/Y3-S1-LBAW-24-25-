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
                @endforeach
            </tbody>
        </table>
    </div>
    <h2>Create New User</h2>
    <div id="userCreatedMessage" class="alert alert-success" style="display: none;">
        User created successfully!
    </div>
    <form id="adminCreateUser">
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

        <button type="button" id="generateUser" data-action-url="{{ route('createUser') }}">Create User</button>
    </form>
</div>
@endsection

