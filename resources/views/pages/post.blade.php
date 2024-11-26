@extends('layouts.app')

@section('content')
    <article class="post" data-id="{{ $post->post_id }}">
        @if((Auth::check() && Auth::user()->user_id == $post->owner->user_id) || (Auth::check() && Auth::user()->isAdmin()))
            <button class="editButton">Edit</button>
            <button class="deleteButton">Delete</button>
            <span class="error"></span>
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
            <p>{{$post->owner->username}} - Published at {{$post->created_at->format('d M Y H:i')}}</p>
            
        </div>
        <div id="postComments">
            <h3>Comments</h3>
            <p>The comments will be displayed here</p>
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