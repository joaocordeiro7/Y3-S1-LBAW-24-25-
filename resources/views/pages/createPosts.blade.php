@extends('layouts.app')


@section('content')
<section id="post_form">
    <form action="" method="POST">
        {{csrf_field()}}

        <label for="title">Title</label>
        <input id="newsTitle" name="newsTitle" type="text" value="Write the title of your news here">

        <label for="body">News body</label>
        <input id="newsBody" name="newsBody" type="text" value="Write your news here">
        <input name="submitButton" type="submit" value="Post">
    </form>
</section>