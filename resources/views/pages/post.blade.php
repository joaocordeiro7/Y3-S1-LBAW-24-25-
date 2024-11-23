@extends('layouts.app')

@section('content')
    <article class="post" data-id="{{ $post->post_id }}">
        <button class="editButton">Edit</button>
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
            <p>Author: {{$post->owner->username}}</p>
            <p>Published at {{$post->created_at->format('d M Y H:i')}}</p>
        </div>
        <div id="postComments">
            <h3>Comments</h3>
            <p>The comments will br displayed here</p>
        </div>
    </article>
    <section class="postEditForm hidden" data-id="{{ $post->post_id }}">
        <form>
            {{csrf_field()}}
            <input type="text" id="newTitle" value="{{$post->title}}">
            <input type="text" id="newBody" value="{{$post->body}}">
            <button type="submit" class="saveButton">Save Changes</button>
        </form>
        <button class="cancelButton">Cancel</button>
    </section>
@endsection