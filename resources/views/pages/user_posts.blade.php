@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts by {{ $user->username }}</h1>

    @if($posts->isEmpty())
        <p>No posts available.</p>
    @else
        <span class="error"></span>
        <ul id="postsList" style="list-style-type: none; padding: 0;">
            @foreach($posts as $post)
                <li id="{{$post->post_id}}">
                    <h2>{{ html_entity_decode($post->title, ENT_QUOTES, 'UTF-8') }}</h2>
                    <p>{{ \Illuminate\Support\Str::words(html_entity_decode($post->body, ENT_QUOTES, 'UTF-8'), 25, '...') }}</p>
                    <a href="{{ url('/post/' . $post->post_id) }}">Read More</a>
                    
                    @can('delete', $post)
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
