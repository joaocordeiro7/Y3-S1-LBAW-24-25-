<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


use Illuminate\View\View;

class PostController extends Controller
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
        $this->authorize('create',Post::class);
        return view('pages.createPosts');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $newPost= new Post();

        $this->authorize('store',$newPost);

        $newPost->title=$request->input('newsTitle');
        $newPost->body=$request->input('newsBody');
        $newPost->ownerid=Auth::user()->user_id;
        $newPost->save();

        return redirect()->intended('/createPosts');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $post = Post::findOrFail($id);
        return view('pages.post',['post'=>$post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        //check policy

        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->updated_at = $request->input('timestamp');

        $post->save();

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
