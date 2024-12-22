@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Posts with Tag: {{ $tagName }}</h1>

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
