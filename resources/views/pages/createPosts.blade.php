@extends('layouts.app')


@section('content')
<section id="post_form">
    <form action="{{ route('publish')}}" method="POST">
        {{csrf_field()}}

        <label for="title">Title</label>
        <input id="newsTitle" name="newsTitle" type="text" placeholder="Write the title of your news here">

        <label for="body">News body</label>
        <input id="newsBody" name="newsBody" type="text" placeholder="Write your news here">
        <button type="submit">Publish</button>
    </form>
</section>
@endsection