<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Notifications\UserNotification;
use Illuminate\View\View;


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
    
            $user->username = "[Deleted Account]";
            $user->email = "deleted{$user->user_id}@user.com";
            $user->password = null; 
            $user->remember_token = null; 
            $user->save();
    
        });

        $user->image()->delete();
        $user->image()->create(['path' => 'images/profile/default.png']);
    
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
        if (!is_numeric($id)) {
            abort(404, 'User not found');
        }
    
        $user = User::find($id);
    
        if (!$user) {
            abort(404, 'User not found');
        }
        //change to profileOwner
        $currentUser = Auth::check() && Auth::id() == $user->user_id;
    
        $canAdminEdit = Auth::check() && Auth::user()->isAdmin();

        $upvotes = Post::where('ownerid', $id)->sum('upvotes') + Comment::where('ownerid', $id)->sum('upvotes');
        $downvotes = Post::where('ownerid', $id)->sum('downvotes') + Comment::where('ownerid', $id)->sum('downvotes');
    
        return view('pages.profile', [
            'user' => $user,
            'currentUser' => $currentUser,
            'canAdminEdit' => $canAdminEdit,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
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
     * Show the form for editing the specified resource.
     */
    public function proposeTopic(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        DB::table('topic_proposal')->insert([
            'title' => htmlspecialchars($validated['title'],ENT_QUOTES,'UTF-8'),
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
        if (str_starts_with($username, '[Deleted')){
            return redirect()->back()->with('error', 'Search for a user that still exists.');
        }
        $user = User::where('username', $username)->first();

        if ($user) {
            return redirect()->route('profile', ['id' => $user->user_id]);
        } else {
            return redirect()->back()->with('error', 'No user found.');
        }
    }

    public function follow(int $userToFollow){
        

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
        

        if(!User::alreadyFollows($userToUnfollow)){

            return response()->json(['fail' => 'dont follow' ]);
        }
        
        
        DB::table('follwed_users') ->where('userid1', Auth::id()) ->where('userid2', $userToUnfollow) ->delete();

        return response()->json(['success' => 'users now follow']);
        
    }


    

    public function getNewNotf(Request $request){
        $newN = UserNotification::getNewNotifs($request);
        return response()->json($newN);
    }

    public static function userNotf(){
        return UserNotification::getNotif();
    }

    public function readNotf(Request $request){
        UserNotification::readNotf($request);
    }
    public function followers($id) {
        $followers = User::whereHas('follows', function ($query) use ($id) {
            $query->where('userid2', $id);
        })->get(['user_id', 'username']);
    
        return response()->json($followers);
    }
    
    public function following($id) {
        $following = User::whereHas('followedBy', function ($query) use ($id) {
            $query->where('userid1', $id);
        })->get(['user_id', 'username']);
    
        return response()->json($following);
    }

    public function followedTags($userId){
        $tags = DB::table('followed_tags')->join('tag','followed_tags.tagid','=','tag.tag_id')->where('followed_tags.userid',$userId)->select('tag.name')->get();
        return response()->json($tags);
    }

    public function followTag(Request $request){
        $id = DB::table('tag')->where('name',$request['name'])->select('tag_id')->first();
        \Log::info('Tag id:',['id'=>$request['name']]);
        if($id==null){
            return response()->json(['fail'=>'invalid tag try reloading the page']);
        }
        else{
            DB::table('followed_tags')->insert([
                'tagid' => $id->tag_id,
                'userid' => Auth::id()
            ]);
            return response()->json(['success'=>'user now follows the tag']);
        }
    }

    public function unfollowTag(Request $request){
        $id = DB::table('tag')->where('name',$request['name'])->select('tag_id')->first();
        if($id==null){
            return response()->json(['fail'=>'invalid tag try reloading the page']);
        }
        else{
            DB::table('followed_tags') ->where('userid', Auth::id()) ->where('tagid', $id->tag_id) ->delete();
            return response()->json(['success'=>'user now does not follow the tag']);
        }
        
    }
}
