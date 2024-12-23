<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Blocked;
use App\Models\TopicProposal;
use App\Models\Tag;


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

        $users = User::where('username', 'not like', '[Deleted%]')->paginate(10, ['*'], 'users_page'); 
        $proposals = TopicProposal::paginate(10, ['*'], 'proposals_page');

        return view('pages.adminDashboard', compact('users', 'proposals'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:250',
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, '[Deleted')) {
                        $fail('The username cannot start with "[Deleted".');
                    }
                },
            ],
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            
            'username' => htmlspecialchars($request->username,ENT_QUOTES,'UTF-8'),
            'email' => htmlspecialchars($request->email,ENT_QUOTES,'UTF-8'),
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
            'username' => [
                'required',
                'string',
                'max:250',
                'unique:users,username,' . $user->user_id . ',user_id',
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, '[Deleted')) {
                        $fail('The username cannot start with "[Deleted".');
                    }
                },
            ], 
            'email' => 'required|email|max:250|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|mimes:png,jpeg,jpg|max:2048'
        ]);
        
        $user->username = htmlspecialchars($request->input('username'),ENT_QUOTES,'UTF-8');
        $user->email = htmlspecialchars($request->input('email'),ENT_QUOTES,'UTF-8');
    
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
     * Accept topic proposal.
     */
    public function acceptTopicProposal($id)
    {
        $proposal = TopicProposal::findOrFail($id);
    
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }
        //dar decode
        $existingTag = Tag::where('name', $proposal->title)->first();
        if ($existingTag) {
            $proposal->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Topic proposal already exists as a tag and has been removed from the proposals.',
            ]);
        }
        //dar decode
        $tag = Tag::create([
            'name' => $proposal->title,
        ]);

    
        $proposal->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Topic accepted and added as a new tag.',
        ]);
    }
    

    /**
     * Discard topic proposal.
     */
    public function discardTopicProposal($id)
    {
        $proposal = TopicProposal::findOrFail($id);
        
        if (!$proposal) {
            return response()->json(['error' => 'Proposal not found'], 404);
        }

        $proposal->delete();

        return response()->json(['success' => true, 'message' => 'Topic proposal discarded']);
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
     * Unblock a user.
     */
    public function unblockUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        DB::table('blocked')->where('blocked_id', $id)->delete();
    
        return response()->json([
            'success' => true,
            'message' => "{$user->username} has been unblocked.",
        ]);
    }
    

    /**
     * Block a user.
     */
    public function blockUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Admins cannot be blocked.',
            ], 403);
        }
    
        DB::table('blocked')->insert(['blocked_id' => $id]);
    
        return response()->json([
            'success' => true,
            'message' => "{$user->username} has been blocked.",
        ]);
    }

    public function adminDeleteAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        DB::transaction(function () use ($user) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    
            DB::table('blacklist')->insert(['email' => $user->email]);
    
            $user->username = "[Deleted Account]";
            $user->email = "deleted{$user->user_id}@user.com";
            $user->password = null;
            $user->remember_token = null;
            $user->image()->delete();
            $user->image()->create(['path' => 'images/profile/default.png']);    
            $user->save();
        });
    
        return response()->json([
            'success' => true,
            'message' => 'User account deleted successfully.'
        ]);
    }

    public function promoteToAdmin($id) {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return response()->json(['error' => 'User is already an admin'], 400);
        }

        Admin::create(['admin_id' => $user->user_id]);

        return response()->json([
            'success' => true,
            'message' => "{$user->username} has been promoted to admin."
        ]);
    }

}
