<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


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
        $validatedRequest=$request->validate(['newsTitle'=>'required','newsBody'=>'required']);
        $newPost= new Post();

        $this->authorize('store',$newPost);

        $newPost->title=$validatedRequest['newsTitle'];
        $newPost->body=$validatedRequest['newsBody'];
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

        $validatedRequest=$request->validate(['title'=>'required','body'=>'required']);
        
        $this->authorize('update',$post);

        $post->title = $validatedRequest['title'];
        $post->body = $validatedRequest['body'];
        $post->updated_at = $request->input('timestamp');

        $post->save();

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, $id)
    {
        $post=Post::find($id);
        $this->authorize('delete',$post);

        try{
            $post->delete();
        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json($ex->getMessage());
        }

        return response()->json(['success' => 'The post was deleted successfully']);
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
            // Retorna todos os posts caso não haja 
            $posts = DB::table('posts')->paginate(10);
        }

        return view('pages.home', ['posts' => $posts]);
    }

    public function showUserPosts($id)
    {
        $user = User::findOrFail($id);
        $posts = Post::where('ownerid', $id)->get();

        return view('pages.user_posts', compact('user', 'posts'));
    }

    public function like (Request $request) {
      
        $post = Post::find($request->id);
        $this->authorize('like', Post::class);
    
        PostLike::insert([
            'user_id' => Auth::user()->id,
            'post_id' => $post->id,
        ]);
    }


}


