@extends('layouts.app')

@section('content')
    <!-- Barra de Pesquisa -->
    <form method="GET" action="{{ route('home') }}" class="mb-4 d-flex">
        <div class="form-group me-2">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" class="form-control" 
                   placeholder="Search posts" value="{{ request('search') }}">
        </div>
        <div class="form-group me-2">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="form-group">
            <a href="{{ route('home') }}" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <!-- Barra de Pesquisa de Username -->
     @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form method="GET" action="{{ route('user.search') }}" class="mb-4">
        <div class="form-group">
            <label for="username">Search Username:</label>
            <input type="text" name="username" id="username" class="form-control" 
                placeholder="Enter username" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>


    <!-- Exibição dos Posts -->
    <div class="container">
        <h1>Posts</h1>
        
        @if ($posts->isEmpty())
            <!-- Mensagem de Nenhum Resultado -->
            <p>No posts found.</p>
        @else
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

        @endif
    </div>
@endsection
