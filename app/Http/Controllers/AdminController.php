<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'You are not authorized to access this page.');
        }

        $users = User::paginate(10);

        return view('pages.adminDashboard', compact('users'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $defaultProfilePicturePath = 'images/profile/default.png';
        $user->image()->create(['path' => $defaultProfilePicturePath]);    

        return response()->json([
            'success' => true,
            'user_id' => $user->user_id,
            'username' => $user->username,
            'email' => $user->email,
            'image_path' => $defaultProfilePicturePath,
        ]);
    }

    public function adminUpdateUser(Request $request, $id)
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
    
            if ($user->image && $user->image->path !== 'images/profile/default.png') {
                Storage::disk('public')->delete($user->image->path);
            }
    
            if ($user->image) {
                $user->image->update(['path' => $path]);
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

    public function adminDeleteAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
    
        if ((Auth::check() && Auth::user()->user_id !== $user->user_id) && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        DB::transaction(function () use ($user) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
            
            //updates  
            DB::table('comments')
                ->where('ownerid', $user->user_id)->update(['ownerid' => 1]);
    
            DB::table('posts')
                ->where('ownerid', $user->user_id)->update(['ownerid' => 1]);
            
            //deletes
     
            $user->delete();
        });
        
        return response()->json([
            'success' => true, 
            'message' => 'User account deleted successfully.'
        ]);
    } 
    
}
