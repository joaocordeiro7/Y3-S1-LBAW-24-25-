@extends('layouts.app')

@section('content')
    <!-- Barra de Pesquisa -->
    <form method="GET" action="{{ route('home') }}" class="mb-4">
        <div class="form-group">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" class="form-control" 
                   placeholder="Search posts" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Exibição dos Posts -->
    <div class="container">
        <h1>Posts</h1>
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

