@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts by {{ $user->username }}</h1>

    @if($posts->isEmpty())
        <p>No posts available.</p>
    @else
        <span class="error"></span>
        <ul id="postsList">
            @foreach($posts as $post)
                <li id="{{$post->post_id}}">
                    <h2>{{ $post->title }}</h2>
                    <p>{{ \Illuminate\Support\Str::words($post->body, 25, '...') }}</p>
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
