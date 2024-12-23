@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 data-tagName="{{$tagName}}" id="tagName">Posts with Tag: {{ $tagName }}</h1>
    @if(Auth::check())
        @if(Auth::user()->alreadyFollowsTag($tagName))
        <button data-tagName="{{$tagName}}" class="unfollowTag">unfollow tag</button>
        @else
        <button data-tagName="{{$tagName}}" class="followTag">follow tag</button>
        @endif
    <span class="error"></span>
    @endif
    @if ($posts->isEmpty())
        <p>No posts found for this tag.</p>
    @else
        <section id="posts">
            @foreach ($posts as $post)
                <article class="post mb-4">
                    <header>
                        <h2>{{ $post->title }}</h2>
                    </header>
                    <p>{{ \Illuminate\Support\Str::words($post->body, 25, '...') }}</p>
                        <a href="{{ url('/post/' . $post->post_id) }}">Read More</a>
                    </article>
            @endforeach
        </section>
    @endif
</div>
@endsection
