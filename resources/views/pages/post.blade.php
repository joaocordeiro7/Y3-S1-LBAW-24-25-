@extends('layouts.app')

@section('content')
    <article class="post" data-id="{{ $post->post_id }}">
        @if((Auth::check() && Auth::user()->user_id == $post->owner->user_id) || (Auth::check() && Auth::user()->isAdmin()))
            <button class="editButton">Edit</button>
            
        @endif
        <header class="newsTitle">
            <h2>{{$post->title}}</h2>
        </header>
        <div class="newsBody">
            <p>{{$post->body}}</p>
        </div>
        <div id="interactions">
            <span id="upvotes">
                <img src="" alt="image for upvotes" id="upvotesLogo" class="interationLogo">
                <span id="numberOfUpvotes" class="numberOfInterations">{{$post->upvotes}}</span>
            </span>
            <span id="downvotes">
                <img src="" alt="image for downvotes" id="downvotesLogo" class="interationLogo">
                <span id="numberOfDownvotes" class="numberOfInterations">{{$post->downvotes}}</span>
            </span>
        </div>
        <div id="postDetails">
            <p><a href="/users/{{$post->owner->user_id}}">{{$post->owner->username}}</a> - Published at {{$post->created_at->format('d M Y H:i')}}</p>
            
        </div>
        <div id="votes">
            <h4 class="qtd-likes">{{ $post->upvotes }}</h4>
            @if (!$hasLiked)
                <button class="button-like" onclick="like({{ $post->post_id }} , 1)">Upvote</button>
                <button class="button-deslike" onclick="like({{ $post->post_id }} , 0)">Downvote</button>
            @endif
            <h4 class="qtd-deslikes">{{ $post->downvotes }}</h4>
        </div>
        <div id="postComments" class="container">
            <h3 id="comments-title">Comments ({{ count($comments) }})</h3>
            <form action="{{ route('comments.store', $post->post_id) }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                <textarea name="body" required></textarea>
                <button type="submit">Post Comment</button>
            </form>
            @if ($comments->isEmpty())
                <p>No comments found.</p>
            @else
                <section id="comments" aria-labelledby="comments-title" role="region">
                    @foreach ($comments as $comment)
                        <article class="comment">
                            <p>{{ $comment->body }}</p>
                            <p><a href="/users/{{$comment->owner->user_id}}">{{$comment->owner->username}}</a> - Published at {{$comment->created_at->format('d M Y H:i')}}</p>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </article>
    <section class="postEditForm hidden" data-id="{{ $post->post_id }}">
        <form>
            {{csrf_field()}}
            <input type="text" id="newTitle" value="{{$post->title}}" required>
            @if($errors->has('title'))
                <span class="error">{{$errors->first('title')}}</span>
            @endif
            <textarea id="newBody" rows="16" required>{{$post->body}}</textarea>
            @if($errors->has('body'))
                <span class="error">{{$errors->first('body')}}</span>
            @endif
            <button type="submit" class="saveButton">Save Changes</button>
            <button class="cancelButton">Cancel</button>
        </form>
    </section>
@endsection