@extends(layout.app)

@section('content')
    <article id="post">
        <header id="newsTitle">
            <h2>{{$post->title}}</h2>
        </header>
        <div>
            <p>{{$post->body}}</p>
        </div>
        <div id="interactions">
            <span id="upvotes">
                <img src="" alt="image for upvotes" id="upvotesLogo" class="interationLogo">
                <span id="numberOfUpvotes" class="numberOfInterations">{{$post->upvotes}}</span>
            </span>
            <span id="downvotes">
                <img src="" alt="image for upvotes" id="upvotesLogo" class="interationLogo">
                <span id="numberOfDownvotes" class="numberOfInterations">{{$post->upvotes}}</span>
            </span>
        </div>
        <div id="postDetails">
            <p>Author: {{$post->owner()}}</p>
            <p>Published at {{$post->created_at}}</p>
        </div>
        <div id="postComments">
            <h3>Comments</h3>
            <p>The comments will br displayed here</p>
        </div>
    </article>
@endsection