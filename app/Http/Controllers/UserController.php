<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


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
    public function deleteAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Ensure the user is authorized to delete this account
        if (Auth::user()->user_id !== $id && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        DB::transaction(function () use ($user) {
            // Use SERIALIZABLE isolation level
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    
            // Delete votes on posts and comments
            DB::table('InterationPosts')->where('userId', $user->user_id)->delete();
            DB::table('InterationComments')->where('userId', $user->user_id)->delete();
    
            // Transfer ownership of posts and comments to the anonymous user
            DB::table('Posts')->where('ownerId', $user->user_id)->update(['ownerId' => 1]);
            DB::table('Comments')->where('ownerId', $user->user_id)->update(['ownerId' => 1]);
    
            // Remove follower relationships
            DB::table('follwed_users')->where('userId1', $user->user_id)->orWhere('userId2', $user->user_id)->delete();
    
            // Delete the user
            $user->delete();
        });
    
        return response()->json([
            'success' => true, 
            'message' => 'User account deleted successfully.'
        ]);
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
            'image' => 'nullable|mimes:png,jpeg,jpg|max:2048' 
        ]);
    
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();
    
        if ($request->file('image')) {
            $file = $request->file('image');
            $path = $file->store('images/profile', 'public');
    
            $image = $user->image;
            if ($image) {
                Storage::disk('public')->delete($image->path);
                $image->update(['path' => $path]);
            } else {
                $user->image()->create(['path' => $path]);
            }
        }
    
        return response()->json([
            'success' => true,
            'username' => $user->username,
            'email' => $user->email,
            'image_path' => $user->image ? asset('storage/' . $user->image->path) : asset('storage/images/profile/default.png'),
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


    public function search(Request $request)
    {
        $username = $request->input('username');
        $user = User::where('username', $username)->first();

        if ($user) {
            return redirect()->route('profile', ['id' => $user->user_id]);
        } else {
            return redirect()->back()->with('error', 'No user found.');
        }
    }

}
