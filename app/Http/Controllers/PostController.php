<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

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
        $postPage='/post/'.strval($newPost->post_id);
        return redirect()->intended($postPage);
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
        
        $this->authorize('update',$post);

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

    /**
     * List all post titles.
     */
    public function list(): View
    {
        // Obter todos os posts com título e corpo
        $posts = Post::all(['post_id', 'title', 'body']);

        // Passar os dados para a view
        return view('pages.home', ['posts' => $posts]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Se houver pesquisa, usar ILIKE para busca tolerante
        if ($search) {
            $posts = DB::table('posts')
                ->where('title', 'ILIKE', '%' . $search . '%')
                ->orWhere('body', 'ILIKE', '%' . $search . '%')
                ->paginate(10);
        } else {
            // Retorna todos os posts caso não haja busca
            $posts = DB::table('posts')->paginate(10);
        }

        return view('pages.home', ['posts' => $posts]);
    }

}


