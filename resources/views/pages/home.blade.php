@extends('layouts.app')

@section('content')

    @if(session('error'))
            <p>{{ session('error') }}</p>
    @endif
    <div class="d-flex justify-content-between align-items-start gap-5 mt-4">
        <!-- Topics Section -->
        <div class="content d-flex flex-column align-items-center w-100" style="max-width: 250px;">
            <h3 class="align-self-center me-5">Topics</h3>
            <ul class="list-group mb-4">
                @foreach ($tags as $tag)
                    <li class="list-group-item">
                        <a href="{{ route('home', ['tag' => $tag->name]) }}">{{ $tag->name }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-auto">
                <button type="button" class="blue-button btn" id="proposeTopicButton" onclick="openProposalForm()">Propose a Topic</button>
            </div>

            <!-- Proposal Form Modal -->
            <div id="proposalModal" class="modal">
                <div id="proposalForm">
                    <h4 class="mb-4">Propose a New Topic</h4>
                    <form class="my-2" id="proposeTopicForm" method="POST" action="{{ route('proposeTopic') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="topicTitle" class="form-label">Topic Title</label>
                            <input type="text" name="title" id="topicTitle" class="form-control" placeholder="Enter topic title" required>
                        </div>
                        <div class="d-flex justify-content-center gap-4">
                            <button type="button" class="gray-button btn" onclick="closeProposalForm()">Cancel</button>
                            <button type="submit" class="blue-button btn">Propose</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="align-self-center mx-auto search border light-gray-border p-5 pb-3 rounded" style="max-width: 400px;">
            <form method="GET" action="{{ route('home') }}" class="mb-4">
            <label for="search" class="form-label">Looking for news?</label>
            <input 
                type="text" 
                name="search" 
                id="search" 
                class="form-control" 
                placeholder="Search posts" 
                value="{{ request('search') }}" 
                required
            >
            <div class="form-group d-flex justify-content-start mt-3">
                <button type="submit" class="btn blue-button me-3">Search</button>
                <button type="button" class="btn gray-button" onclick="window.location='{{ route('home') }}'">Clear</button>
            </div>
            </form>
            <hr class="my-4">
            <form action="{{ route('user.search') }}" method="GET">
            <label for="usernameSearch" class="form-label">Looking for someone?</label>
            <input 
                type="text" 
                name="username" 
                id="usernameSearch" 
                class="form-control" 
                placeholder="Search for a username" 
                required
            >
            <div class="form-group d-flex justify-content-start mt-3">
                <button type="submit" class="btn blue-button me-3">Search</button>
                <button type="button" class="btn gray-button" onclick="window.location='{{ route('home') }}'">Clear</button>
            </div>
            </form>
        </div>
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
