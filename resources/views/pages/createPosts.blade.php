@extends('layouts.app')


@section('content')
<section id="post_form">
    <form action="{{ route('publish')}}" method="POST">
        {{csrf_field()}}

        <label for="newsTitle">Title</label>
        <input id="newsTitle" name="newsTitle" type="text" placeholder="Write the title of your news here (required)" value="{{old('newsTitle')}}" required>
        @if($errors->has('newsTitle'))
            <span class="error">{{$errors->first('newsTitle')}}</span>
        @endif

        <label for="newsBody">News body</label>
        <textarea id="newsBody" name="newsBody" type="text" placeholder="Write your news here (required)" rows="16" required>{{old('newsTitle')}}</textarea>
        @if($errors->has('newsBody'))
            <span class="error">{{$errors->first('newsBody')}}</span>
        @endif
        <label for="tags">Select Tags</label>
        <div id="tags">
            @foreach ($tags as $tag)
                <div>
                    <input type="checkbox" id="tag{{ $tag->tag_id }}" name="tags[]" value="{{ $tag->tag_id }}">
                    <label for="tag{{ $tag->tag_id }}">{{ $tag->name }}</label>
                </div>
            @endforeach
        </div>
        <button type="submit">Publish</button>
    </form>
</section>
@endsection