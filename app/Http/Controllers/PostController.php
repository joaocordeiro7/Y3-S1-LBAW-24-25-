<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\InteractionPosts;
use App\Models\InteractionComments;
use App\Models\Comment;
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
        $this->authorize('create', Post::class);
        $tags = Tag::all();
        return view('pages.createPosts', ['tags' => $tags]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedRequest = $request->validate(['newsTitle' => 'required', 'newsBody' => 'required', 'tags' => 'array']);
        $newPost = new Post();
    
        $this->authorize('store', $newPost);
    
        $newPost->title = htmlspecialchars($validatedRequest['newsTitle'],ENT_QUOTES,'UTF-8');
        $newPost->body = htmlspecialchars($validatedRequest['newsBody'],ENT_QUOTES,'UTF-8');
        $newPost->ownerid = Auth::user()->user_id;
        $newPost->save();
    
        if ($request->has('tags')) {
            $newPost->tags()->attach($request->input('tags'));
        }
    
        $postPage = '/post/' . strval($newPost->post_id);
        return redirect()->intended($postPage);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        if (!is_numeric($id)) {
            abort(404, 'Post not found');
        }
    
        $post = Post::with('tags')->findOrFail($id);
    
        if (!$post) {
            abort(404, 'Post not found');
        }
        
        $comments = $post->comments;
        return view('pages.post', ['post' => $post, 'comments' => $comments]);
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

        $post->title = htmlspecialchars($validatedRequest['title'],ENT_QUOTES,'UTF-8');
        $post->body = htmlspecialchars($validatedRequest['body'],ENT_QUOTES,'UTF-8');
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
            DB::table('upvoteonpostnotification')->where('post',$id)->delete();
            DB::table('post_tags')->where('post',$id)->delete();
            $post->delete();
        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json(['postId'=>$id]);
        }

        return response()->json(['success' => 'The post was deleted successfully','postId'=>$id]);
    }

    /**
     * List all post titles.
     */
    public function list(): View
    {
        // Obter todos os posts com título e corpo
        $posts = Post::select(['post_id', 'title', 'body'])->orderBy('created_at','desc')->orderBy('upvotes','desc')->paginate(6);
        
        
        $tags = Tag::all();

        // Passar os dados para a view
        return view('pages.home', ['posts' => $posts], ['tags' => $tags]);
    }


    public function index(Request $request) {
        $search = $request->input('search');
        $searchIn = $request->input('search_in', ['title', 'body', 'comments']);
        $sort = $request->input('sort', 'date');
        $order = $request->input('order', 'desc'); 
        $tags = Tag::all();
    
        $query = Post::query();
    
        if ($search) {
            $searchTerms = array_map(function ($term) {
                return trim($term) . ':*';
            }, explode(' ', $search));
            $searchQuery = implode(' | ', $searchTerms);
    
            $query->where(function ($q) use ($searchQuery, $searchIn) {
                if (in_array('title', $searchIn)) {
                    $q->orWhereRaw(
                        "setweight(to_tsvector('english', title), 'A') @@ to_tsquery('english', ?)",
                        [$searchQuery]
                    );
                }
                if (in_array('body', $searchIn)) {
                    $q->orWhereRaw(
                        "setweight(to_tsvector('english', body), 'B') @@ to_tsquery('english', ?)",
                        [$searchQuery]
                    );
                }
                if (in_array('comments', $searchIn)) {
                    $q->orWhereExists(function ($subquery) use ($searchQuery) {
                        $subquery->select(DB::raw(1))
                            ->from('comments')
                            ->whereRaw("comments.post = posts.post_id")
                            ->whereRaw(
                                "comments.tsvectors @@ to_tsquery('english', ?)",
                                [$searchQuery]
                            );
                    });
                }
            });
        }
    
        $validSortColumns = ['date' => 'created_at', 'popularity' => 'upvotes'];
        $sortColumn = $validSortColumns[$sort] ?? 'created_at';
    
        $query->orderBy($sortColumn, $order);
    
        $posts = $query->distinct()->paginate(3);
    
        return view('pages.home', [
            'posts' => $posts,
            'tags' => $tags,
            'sort' => $sort,
            'order' => $order,
            'search_in' => $searchIn,
        ]);
    }    
        
    

    public function showUserPosts($id)
    {
        $user = User::findOrFail($id);
        $posts = Post::where('ownerid', $id)->get();

        return view('pages.user_posts', compact('user', 'posts'));
    }

    public static function getMoreResulsts($page,$search,$searchIn,$sort,$order){
        $query = Post::query();
        
        if ($search) {
            $searchTerms = array_map(function ($term) {
                return trim($term) . ':*';
            }, explode(' ', $search));
            $searchQuery = implode(' | ', $searchTerms);
    
            $query->where(function ($q) use ($searchQuery, $searchIn) {
                if (in_array('title', $searchIn)) {
                    $q->orWhereRaw(
                        "setweight(to_tsvector('english', title), 'A') @@ to_tsquery('english', ?)",
                        [$searchQuery]
                    );
                }
                if (in_array('body', $searchIn)) {
                    $q->orWhereRaw(
                        "setweight(to_tsvector('english', body), 'B') @@ to_tsquery('english', ?)",
                        [$searchQuery]
                    );
                }
                if (in_array('comments', $searchIn)) {
                    $q->orWhereExists(function ($subquery) use ($searchQuery) {
                        $subquery->select(DB::raw(1))
                            ->from('comments')
                            ->whereRaw("comments.post = posts.post_id")
                            ->whereRaw(
                                "comments.tsvectors @@ to_tsquery('english', ?)",
                                [$searchQuery]
                            );
                    });
                }
            });
        }
    
        $validSortColumns = ['date' => 'created_at', 'popularity' => 'upvotes'];
        $sortColumn = $validSortColumns[$sort] ?? 'created_at';
    
        $query->orderBy($sortColumn, $order);
    
        
    
        $posts = $query->distinct()->paginate(3,['*'],'page',$page);
    
        
        
        return $posts;
    }

    public function getMorePosts(Request $request){
        $page = $request['page']+2;
        
        if($request['search']=="" && !isset($request['sort']) && !isset($request['order'])){
            $posts = Post::select(['post_id', 'title', 'body'])->orderBy('created_at','desc')->orderBy('upvotes','desc')->paginate(6,['*'],'page',$page);
            
            
        }
        else{
            
            $searchIn = ['title','body','comments'];
            
            if($request->input('title')==='false'){
                
                $searchIn = array_filter($searchIn, function($item) { return $item !== 'title'; });
            }
            if($request->input('body')==='false'){
                $searchIn = array_filter($searchIn, function($item) { return $item !== 'body'; });
            }
            if($request->input('comments')==='false'){
                $searchIn = array_filter($searchIn, function($item) { return $item !== 'comments'; });
            }
            $searchIn = array_values($searchIn);
            if(empty($searchIn)){
                $searchIn = ['title','body','comments'];
            }
            $posts = PostController::getMoreResulsts($page,$request['search'],$searchIn,$request['sort'],$request['order']);

            
            
        }

        return response()->json($posts);
    }
    public function like (Request $request) {

        try {
            $post = Post::findOrFail($request->post_id); 
            $simnao = $request->liked; 
        
            $liked = true;

            if ($simnao == 0) {
                $liked = false;
            }
            
            $this->authorize('like', Post::class);
                    
            $existing = InteractionPosts::where('userid', Auth::user()->user_id)
                                            ->where('postid', $post->post_id)
                                            ->first();
            
            $success = true;
            $error = "";

            if (!$existing) {
                // Gravar o like
                
                InteractionPosts::insert([
                    'userid' => Auth::user()->user_id,
                    'postid' => $post->post_id,
                    'liked' => $liked,
                ]);
            } else {
                if ($existing->liked !== $liked) {
                    // Update para o oposto
                    InteractionPosts::where('id', $existing->id)->update(['liked' => $liked]);
                } else {
                    // delete
                    $existing->delete();
                }
            }
            
            $postLikes = InteractionPosts::where('postid', $post->post_id)
                                        ->where('liked', true);
            
            $postDeslikes = InteractionPosts::where('postid', $post->post_id)
                                            ->where('liked', false);
                                            
            // Retornar o número atualizado de likes
            return response()->json([
                'success' => $success,
                "likes" => $postLikes->count(),
                "deslikes" => $postDeslikes->count(),
                "error" => $error
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function voteComment(Request $request) {
        try {
            $comment = Comment::findOrFail($request->comment_id); 
            $simnao = $request->liked; 
        
            $liked = true;

            if ($simnao == 0) {
                $liked = false;
            }
            
            $this->authorize('voteComment', Post::class);
                    
            $existing = InteractionComments::where('userid', Auth::user()->user_id)
                                            ->where('comment_id', $comment->comment_id)
                                            ->first();
            
            $success = true;
            $error = "";

            if (!$existing) {
                // Gravar o like
                
                InteractionComments::insert([
                    'userid' => Auth::user()->user_id,
                    'comment_id' => $comment->comment_id,
                    'liked' => $liked,
                ]);
            } else {
                if ($existing->liked !== $liked) {
                    // Update para o oposto
                    InteractionComments::where('id', $existing->id)->update(['liked' => $liked]);
                } else {
                    // delete
                    $existing->delete();
                }
            }
            
            $commentLikes = InteractionComments::where('comment_id', $comment->comment_id)
                                        ->where('liked', true);
            
            $commentDeslikes = InteractionComments::where('comment_id', $comment->comment_id)
                                            ->where('liked', false);
                                            
            // Retornar o número atualizado de likes
            return response()->json([
                'success' => $success,
                "upvotes" => $commentLikes->count(),
                "downvotes" => $commentDeslikes->count(),
                "error" => $error
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function storeComment(Request $request) {
        $request->validate([
            'body' => 'required|string|max:1000', 
            'post_id' => 'required|exists:posts,post_id', 
        ]);
    
        $comment = Comment::create([
            'ownerid' => Auth::user()->user_id,
            'post' => $request->post_id,
            'body' => htmlspecialchars($request->body,ENT_QUOTES,'UTF-8'),
        ]);
    
        return response()->json([
            'success' => true,
            'comment' => $comment,  
        ]);
    }
       

    public function updateComment(Request $request, $id) {
        $request->validate(['body' => 'required|string|max:1000']);
    
        $comment = Comment::findOrFail($id);
    
        if ($comment->ownerid != Auth::user()->user_id) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }
    
        $comment->body = $request->input('body');
        $comment->save();
    
        return response()->json(['success' => true]);
    }   
    
    
    public function replyToComment(Request $request) {
        $request->validate([
            'body' => 'required|string|max:1000',
            'reply_to' => 'required|exists:comments,comment_id',
        ]);

        $reply = Comment::create([
            'body' => $request->body,
            'reply_to' => htmlspecialchars($request->reply_to,ENT_QUOTES,'UTF-8'),
            'ownerid' => Auth::user()->user_id,
            'post' => Comment::findOrFail($request->reply_to)->post,
        ]);

        return response()->json([
            'success' => true,
            'reply' => $reply->load('owner'),
        ]);
    }


    public function deleteComment(Request $request, $id) {
        try {
            $comment = Comment::findOrFail($id);

            // Verifica autorização
            if (Auth::user()->user_id !== $comment->ownerid && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'error' => 'You are not authorized to delete this comment.'], 403);
            }

            // Verifica se há replies
            $hasReplies = Comment::where('reply_to', $id)->exists();
            if ($hasReplies) {
                return response()->json(['success' => false, 'error' => 'You cannot delete a comment that has replies.']);
            }

            // Verifica se há upvotes ou downvotes
            $hasVotes = InteractionComments::where('comment_id', $id)->exists();
            if ($hasVotes) {
                return response()->json(['success' => false, 'error' => 'You cannot delete a comment with upvotes or downvotes.']);
            }

            // Apaga o comentário
            DB::table('upvoteoncommentnotification')->where('liked_comment',$id)->delete();
            DB::table('commentnotification')->where('comment',$id)->delete();
            $comment->delete();

            return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function filterByTag(string $tagName): View {
        $tag = Tag::where('name', $tagName)->first();
    
        if (!$tag) {
            return redirect()->route('home')->with('error', 'Tag not found.');
        }
    
        $posts = $tag->posts()->with('tags')->paginate(6);
    
        return view('pages.postsByTag', [
            'posts' => $posts,
            'tagName' => $tagName,
        ]);
    }

    public function getMoreTagPosts(Request $request){
        $page = $request['page']+2;
        $tag = Tag::where('name', $request['name'])->first();
    
        if (!$tag) {
            return redirect()->route('home')->with('error', 'Tag not found.');
        }
    
        $posts = $tag->posts()->with('tags')->paginate(6,['*'],'page',$page);

        return response()->json($posts);

    }
    


}