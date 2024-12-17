@extends('layouts.app')

@section('content')

    @if(session('error'))
            <p>{{ session('error') }}</p>
    @endif
    <div class="content" style="max-width: 215px;">    
        <div class="d-flex flex-column h-100">
            <h3>Topics</h3>
                <ul class="list-group mb-4">
                    @foreach ($tags as $tag)
                        <li class="list-group-item">
                            <a href="{{ route('home', ['tag' => $tag->name]) }}">{{ $tag->name }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-auto">
                <button type="button" class="btn btn-primary w-100" id="proposeTopicButton" onclick="openProposalForm()">Propose a Topic</button>            </div>
        </div>

        <!-- Proposal Form Modal -->
        <div id="proposalModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.5); z-index:1050;">
            <div class="modal-dialog" style="margin:10% auto; background:white; padding:20px; max-width:400px; border-radius:8px;">
                <h4 class="mb-4">Propose a New Topic</h4>
                <form id="proposeTopicForm" method="POST" action="{{ route('proposeTopic') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="topicTitle" class="form-label">Topic Title</label>
                        <input type="text" name="title" id="topicTitle" class="form-control" placeholder="Enter topic title" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="closeProposalForm()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Propose</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Barra de Pesquisa -->
    <div class="search" style="margin: 0 auto; padding: 1em; max-width: 20em; border: 1px solid #d2afe7; position: absolute; top: 10em; right: 50em; width: 25%;">
        <form method="GET" action="{{ route('home') }}" class="mb-4 d-flex flex-column align-items-center gap-2" style="border-bottom: 1em;">
            <label for="search" class="visually-hidden">Looking for a news?</label>
            <input type="text" name="search" id="search" placeholder="Search posts" value="{{ request('search') }}" required>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="form-group">
                <a href="{{ route('home') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>

        <form action="{{ route('user.search') }}" method="GET" class="mb-4 d-flex flex-column gap-2" style="border-bottom: 1em;">
            <label for="search" class="visually-hidden">Looking for someone?</label>
            <input type="text" name="username" placeholder="Search for a username" required>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="form-group">
                <a href="{{ route('home') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    <!-- Exibição dos Posts -->
    <div class="container" style="margin-top: 5em">
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
