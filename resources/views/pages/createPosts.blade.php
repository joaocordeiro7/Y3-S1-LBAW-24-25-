@extends('layouts.app')


@section('content')
<section id="post_form">
    <form action="{{ route('publish')}}" method="POST">
        {{csrf_field()}}

        <label for="newsTitle">Title</label>
        <input id="newsTitle" name="newsTitle" type="text" placeholder="Write the title of your news here" value="{{old('newsTitle')}}" required>
        @if($errors->has('newsTitle'))
            <span class="error">{{$errors->first('newsTitle')}}</span>
        @endif

        <label for="newsBody">News body</label>
        <textarea id="newsBody" name="newsBody" type="text" placeholder="Write your news here" rows="16" required>{{old('newsTitle')}}</textarea>
        @if($errors->has('newsBody'))
            <span class="error">{{$errors->first('newsBody')}}</span>
        @endif
        <button type="submit">Publish</button>
    </form>
</section>
@endsection