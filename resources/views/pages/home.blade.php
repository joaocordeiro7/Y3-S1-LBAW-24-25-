@extends('layouts.app')

@section('content')

    @if(session('error'))
            <p>{{ session('error') }}</p>
    @endif
    <div class="container mt-4">
        <div class="d-flex justify-content-start align-items-start gap-4">
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
                        <button style="font-size: 1.25rem; margin-left: 30px"type="button" class="btn btn-primary w-90" id="proposeTopicButton" onclick="openProposalForm()">Propose a Topic</button>            </div>
                </div>

                <!-- Proposal Form Modal -->
                <div id="proposalModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050;">
                    <div style="margin: 10% auto; background: white; padding: 20px; max-width: 400px; border-radius: 8px;">
                        <h4 class="mb-4">Propose a New Topic</h4>
                        <form id="proposeTopicForm" method="POST" action="{{ route('proposeTopic') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="topicTitle" class="form-label">Topic Title</label>
                                <input type="text" name="title" id="topicTitle" class="form-control" placeholder="Enter topic title" style="font-size:1.5rem;"required>
                            </div>
                            <div class="d-flex justify-content-end gap-4">
                                <button type="button" class="btn btn-secondary" onclick="closeProposalForm()" style="font-size:1.2rem;">Cancel</button>
                                <button type="submit" class="btn btn-primary" style="font-size:1.2rem;">Propose</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Barra de Pesquisa -->
            <div class="d-flex justify-content-center gap-4" style="flex-grow: 1;">
                <div class="search" style="padding: 1em; border: 1px solid rgb(10, 10, 10); max-width: 25em;">
                    <form method="GET" action="{{ route('home') }}" class="d-flex flex-column gap-2">
                        <label for="search">Looking for a news?</label>
                        <p style="font-size: 0.8em; color: rgb(88,88,88)">Use Exact Match Search</p>
                        <input type="text" name="search" id="search" placeholder="Search posts" value="{{ request('search') }}" required>
                        <div class="form-group d-flex flex-row justify-content-between align-items-center gap-2">
                            <button type="submit" class="btn" style="background-color: #0e96c2; color:white; font-size: 1.25rem;">Search</button>
                            <a href="{{ route('home') }}" class="btn btn-lg" style="font-size: 1.4rem; background-color: #a9a9a9; color: black; max-height: 30px;">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="search" style="padding: 1em; border: 1px solid rgb(10, 10, 10); max-width: 25em;">
                    <form action="{{ route('user.search') }}" method="GET" class="d-flex flex-column gap-2">
                        <label for="search">Looking for someone?</label>
                        <p style="font-size: 0.8em; color: rgb(88,88,88)">Use Full-Text Search</p>
                        <input type="text" name="username" placeholder="Search for a username" required>
                        <div class="form-group d-flex flex-row justify-content-between gap-2">
                            <button type="submit" class="btn" style="background-color: #0e96c2; color:white; font-size: 1.25rem;">Search</button>
                            <a href="{{ route('home') }}" class="btn btn-lg" style="font-size: 1.4rem; background-color: #a9a9a9; color: black; max-height: 30px;">Clear</a>
                        </div>
                    </form>
                </div>
            </div>
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