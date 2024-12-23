@extends('layouts.app')

@section('content')
    <article class="post" data-id="{{ $post->post_id }}">
        @if((Auth::check() && Auth::user()->user_id == $post->owner->user_id) || (Auth::check() && Auth::user()->isAdmin()))
            <button class="editButton">Edit</button>
            
        @endif
        <header class="newsTitle">
            
            <h2>{{html_entity_decode($post->title, ENT_QUOTES, 'UTF-8')}}</h2>
        </header>
        <div class="newsBody">
            <pre>{{html_entity_decode($post->body, ENT_QUOTES, 'UTF-8')}}</pre>
        </div>
        
        <div id="postDetails">
            <div class="post-tags">
                <span class="tags-list">
                    <h4>Tags:</h4>
                    @foreach ($post->tags as $tag)
                        <span>{{ $tag->name }}</span>
                    @endforeach
                </span>
            </div>
            <p><a href="/users/{{$post->owner->user_id}}">{{html_entity_decode($post->owner->username, ENT_QUOTES, 'UTF-8')}}</a> - Published at {{$post->created_at->format('d M Y H:i')}}</p>
            <div id="votes">
                @if (Auth::check() && Auth::user()->user_id != $post->owner->user_id)
                    <button onclick="like({{ $post->post_id }} , 1)"><i class="fa-regular fa-thumbs-up"></i></button>
                    <span class="qtd-likes">{{ $post->upvotes }}</span>
                    <button onclick="like({{ $post->post_id }} , 0)"><i class="fa-regular fa-thumbs-down"></i></button>
                    <span class="qtd-deslikes">{{ $post->downvotes }}</span>
                @else
                    <span class="qtd-likes"><i class="fa-regular fa-thumbs-up"></i> {{ $post->upvotes }}</span>
                    <span class="qtd-deslikes"><i class="fa-regular fa-thumbs-down"></i> {{ $post->downvotes }}</span>
                @endif
            </div>
        </div>
    </article>
    <section class="comments-section">
        <div id="postComments" class="container">
            <h3 id="comments-title">Comments ({{ count($comments) }})</h3>
            @if (!Auth::check())
                <a href="/login">Login to see more.</a>
            @else
                <form id="post-comment-form" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                    <textarea id="commentsBody" name="body" type="text" placeholder="Write your comment here" required></textarea>
                    @if($errors->has('commentsBody'))
                        <span class="error">{{$errors->first('commentsBody')}}</span>
                    @endif
                    <button type="submit">Post Comment</button>
                </form>
                <section id="comments" aria-labelledby="comments-title" role="region">
                    @if ($comments->isEmpty())
                        <p>No comments found.</p>
                    @else
                        @foreach ($comments as $comment)
                            @if ($comment->reply_to === null)
                                <article class="comment" data-comment-id="{{ $comment->comment_id }}">
                                    <p id="comment-body-{{ $comment->comment_id }}">{{ html_entity_decode($comment->body, ENT_QUOTES, 'UTF-8') }}</p>
                                    <p><a href="/users/{{$comment->owner->user_id}}">{{html_entity_decode($comment->owner->username, ENT_QUOTES, 'UTF-8')}}</a> - Published at {{$comment->created_at->format('d M Y H:i')}}</p>
                                    @if (Auth::check() && (Auth::user()->user_id == $comment->owner->user_id || Auth::user()->isAdmin()))
                                        <button onclick="deleteComment({{ $comment->comment_id }})" class="delete-comment-btn">Delete</button>
                                    @endif
                                    @if (Auth::check() && Auth::user()->user_id == $comment->owner->user_id)
                                        <button class="edit-comment-btn" onclick="editComment({{ $comment->comment_id }})">Edit</button>
                                        <form id="edit-comment-form-{{ $comment->comment_id }}" class="hidden" method="POST" action="{{ route('comments.update', $comment->comment_id) }}">
                                            @csrf
                                            @method('PUT')
                                            <textarea id="editedBody" name="body" type="text" placeholder="Rewrite your comment here" required>{{ html_entity_decode($comment->body, ENT_QUOTES, 'UTF-8') }}</textarea>
                                            <button type="button" onclick="saveEditedComment({{ $comment->comment_id }})">Save</button>
                                            <button type="button" onclick="cancelEdit({{ $comment->comment_id }})">Cancel</button>
                                        </form>
                                        <div class="comment-votes">
                                            <span id="upvotes-{{ $comment->comment_id }}"><i class="fa-regular fa-thumbs-up"></i> {{ $comment->upvotes ?? 0 }}</span>
                                            <span id="downvotes-{{ $comment->comment_id }}"><i class="fa-regular fa-thumbs-down"></i> {{ $comment->downvotes ?? 0 }}</span>
                                        </div>
                                    @else
                                        <div class="comment-votes">
                                            <button onclick="voteComment({{ $comment->comment_id }}, 1)"><i class="fa-regular fa-thumbs-up"></i></button>
                                            <span id="upvotes-{{ $comment->comment_id }}">{{ $comment->upvotes ?? 0 }}</span>
                                            <button onclick="voteComment({{ $comment->comment_id }}, 0)"><i class="fa-regular fa-thumbs-down"></i></button>
                                            <span id="downvotes-{{ $comment->comment_id }}">{{ $comment->downvotes ?? 0 }}</span>
                                        </div>
                                    @endif
                                    <button class="reply-comment-btn" onclick="toggleReplyForm({{ $comment->comment_id }})">Reply</button>
                                    <form id="reply-form-{{ $comment->comment_id }}" class="hidden" method="POST">
                                        @csrf
                                        <textarea id="editedBody" name="body" type="text" placeholder="Write your reply here" require></textarea>
                                        <input type="hidden" name="reply_to" value="{{ $comment->comment_id }}">
                                        <button type="button" onclick="postReply({{ $comment->comment_id }})">Post Reply</button>
                                    </form>
                                    @if ($comment->replies->isNotEmpty())
                                        <div class="replies">
                                            @foreach ($comment->replies as $reply)
                                                <article class="reply" data-reply-id="{{ $reply->comment_id }}">
                                                    <p id="reply-body-{{ $reply->comment_id }}">{{ html_entity_decode($reply->body, ENT_QUOTES, 'UTF-8') }}</p>
                                                    <p><a href="/users/{{ $reply->owner->user_id }}">{{ html_entity_decode($reply->owner->username, ENT_QUOTES, 'UTF-8') }}</a> - Published at {{ $reply->created_at->format('d M Y H:i') }}</p>
                                                    @if (Auth::check() && (Auth::user()->user_id == $reply->owner->user_id || Auth::user()->isAdmin()))
                                                        <button onclick="deleteReply({{ $reply->comment_id }})" class="delete-comment-btn">Delete</button>
                                                    @endif
                                                    @if (Auth::check() && Auth::user()->user_id == $reply->owner->user_id)
                                                        <button class="edit-comment-btn" onclick="editReply({{ $reply->comment_id }})">Edit</button>
                                                        <form id="edit-reply-form-{{ $reply->comment_id }}" class="hidden" method="POST" action="{{ route('comments.update', $reply->comment_id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <textarea id="editedBody" name="body" type="text" placeholder="Rewrite your reply here" required>{{ html_entity_decode($reply->body, ENT_QUOTES, 'UTF-8') }}</textarea>
                                                            <button type="button" onclick="saveEditedReply({{ $reply->comment_id }})">Save</button>
                                                            <button type="button" onclick="cancelEditReply({{ $reply->comment_id }})">Cancel</button>
                                                        </form>
                                                        <div class="comment-votes">
                                                            <span id="upvotes-{{ $reply->comment_id }}"><i class="fa-regular fa-thumbs-up"></i> {{ $reply->upvotes ?? 0 }}</span>
                                                            <span id="downvotes-{{ $reply->comment_id }}"><i class="fa-regular fa-thumbs-down"></i> {{ $reply->downvotes ?? 0 }}</span>
                                                        </div>
                                                    @else
                                                        <div class="comment-votes">
                                                            <button onclick="voteComment({{ $reply->comment_id }}, 1)"><i class="fa-regular fa-thumbs-up"></i></button>
                                                            <span id="upvotes-{{ $reply->comment_id }}">{{ $reply->upvotes ?? 0 }}</span>
                                                            <button onclick="voteComment({{ $reply->comment_id }}, 0)"><i class="fa-regular fa-thumbs-down"></i></button>
                                                            <span id="downvotes-{{ $reply->comment_id }}">{{ $reply->downvotes ?? 0 }}</span>
                                                        </div>
                                                    @endif
                                                </article>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endif
                        @endforeach
                    @endif
                </section>
            @endif
        </div>
    </section>
    <section class="postEditForm hidden" data-id="{{ $post->post_id }}">
        <form>
            {{csrf_field()}}
            <input type="text" id="newTitle" value="{{html_entity_decode($post->title, ENT_QUOTES, 'UTF-8')}}" required>
            @if($errors->has('title'))
                <span class="error">{{$errors->first('title')}}</span>
            @endif
            <textarea id="newBody" rows="16" required>{{html_entity_decode($post->body, ENT_QUOTES, 'UTF-8')}}</textarea>
            @if($errors->has('body'))
                <span class="error">{{$errors->first('body')}}</span>
            @endif
            <button type="submit" class="saveButton">Save Changes</button>
            <button class="cancelButton">Cancel</button>
        </form>
    </section>
@endsection