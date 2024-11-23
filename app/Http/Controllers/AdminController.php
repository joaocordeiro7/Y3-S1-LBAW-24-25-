<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the user is authenticated and an admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            // Redirect non-admin users or unauthenticated users
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        // Logic for admin users
        $users = User::paginate(10); // Example: Fetch all users for admin management

        return view('pages.adminDashboard', compact('users'));
    }

    public function createUserForm()
    {
        return view('pages.createUser');
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return redirect()->route('adminDashboard')->with('success', 'User created successfully!');
    }

    public function adminUpdateUser(Request $request, $id)
    {
        $user = User::find($id);
    
        if (!$user) {
            abort(404, 'User not found.');
        }
    
        $request->validate([
            'username' => 'required|string|max:250|unique:users,username,' . $user->user_id . ',user_id', 
            'email' => 'required|email|max:250|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    
        $user->username = $request->input('username');
        $user->email = $request->input('email');
    
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();
    
        return redirect()->route('profile', ['id' => $user->user_id])
            ->with('success', 'User updated successfully.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
