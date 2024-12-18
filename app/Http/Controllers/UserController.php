<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



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
    
        if (Auth::user()->user_id !== $user->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        DB::transaction(function () use ($user) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    
            $user->username = "deleted{$user->user_id}";
            $user->email = "deleted{$user->user_id}@user.com";
            $user->password = null; 
            $user->remember_token = null; 
            $user->save();
    
        });
    
        Auth::logout(); 
    
        return response()->json([
            'success' => true,
            'message' => 'Your account has been deleted successfully.'
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        //change to profileOwner
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
     * Show the form for editing the specified resource.
     */
    public function proposeTopic(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        DB::table('topic_proposal')->insert([
            'title' => $validated['title'],
        ]);

        return redirect()->back()->with('success', 'Topic proposed successfully!');
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

    public function follow(int $userToFollow){
        //$this->authorize('canFollow',$userToFollow);

        if(User::alreadyFollows($userToFollow) || !Auth::check() || Auth::id()===$userToFollow){

            return response()->json(['fail' => 'cant follow' ]);
        }
        
        
        DB::table('follwed_users')->insert([
            'userid1' => Auth::id(),
            'userid2' => $userToFollow
        ]);

        return response()->json(['success' => 'users now follow']);
        
    }

    public function unfollow(int $userToUnfollow){
        //$this->authorize('canFollow',$userToFollow);

        if(!User::alreadyFollows($userToUnfollow)){

            return response()->json(['fail' => 'dont follow' ]);
        }
        
        
        DB::table('follwed_users') ->where('userid1', Auth::id()) ->where('userid2', $userToUnfollow) ->delete();

        return response()->json(['success' => 'users now follow']);
        
    }

}
