<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show($id)
    {
        $user = User::find($id);
    
        $currentUser = Auth::check() && Auth::id() == $user->user_id;
    
        $canAdminEdit = Auth::check() && Auth::user()->isAdmin();
    
        return view('pages.profile', [
            'user' => $user,
            'currentUser' => $currentUser,
            'canAdminEdit' => $canAdminEdit,
        ]);
    }
    
    /**
     * Show the form for editing the authenticated user's profile.
     */
    public function editUser($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return redirect()->route('profile', ['id' => $id])->with('error', 'User not found.');
        }
    
        if (Auth::id() != $user->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('profile', ['id' => $id])->with('error', 'You are not authorized to edit this profile.');
        }
    
        return view('pages.editProfile', ['user' => $user]);
    }
    

    /**
     * Update the authenticated user's profile.
     */
    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id); 
    
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
    
        return response()->json([
            'success' => true,
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
