@extends('layouts.app')

@section('content')

    @if(session('error'))
            <p>{{ session('error') }}</p>
    @endif

    <!-- Barra de Pesquisa -->
    <form method="GET" action="{{ route('home') }}" class="mb-4 d-flex align-items-center gap-2">
        <div class="form-group">
            <label for="search" class="visually-hidden">Looking for a news?</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Search posts" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="form-group">
            <a href="{{ route('home') }}" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <form action="{{ route('user.search') }}" method="GET">
        <label for="search" class="visually-hidden">Looking for someone?</label>
        <input type="text" name="username" placeholder="Search for a username" required>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="form-group">
            <a href="{{ route('home') }}" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <!-- Exibição dos Posts -->
    <div class="container">
        <h1>Latest Posts</h1>
        
        @if ($posts->isEmpty())
            <!-- Mensagem de Nenhum Resultado -->
            <p>No posts found.</p>
        @else
            <section id="posts">
                @foreach ($posts as $post)
                    <article class="post">
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
