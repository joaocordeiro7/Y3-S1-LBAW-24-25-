@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts by {{ $user->username }}</h1>

    @if($posts->isEmpty())
        <p>No posts available.</p>
    @else
        <ul>
            @foreach($posts as $post)
                <li>
                    <h2>{{ $post->title }}</h2>
                    <p>{{ \Illuminate\Support\Str::words($post->body, 25, '...') }}</p>
                    <a href="{{ url('/post/' . $post->post_id) }}">Read More</a>
                    
                    @can('delete', $post)
                        <form action="{{ route('post.delete', ['id' => $post->post_id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    @endcan
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
