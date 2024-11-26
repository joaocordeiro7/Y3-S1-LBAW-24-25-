@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Latest News</h1>
        <section id="cards">
            @foreach ($posts as $post)
                <article class="card">
                    <header>
                        <h2>{{ $post->title }}</h2>
                    </header>
                    <p>{{ \Illuminate\Support\Str::words($post->body, 25, '...') }}</p>
                    <a href="{{ url('/post/' . $post->post_id) }}">Read More</a>
                </article>
            @endforeach
        </section>
    </div>
@endsection
