
    <ol id="notfs" class="dropdown-content">
    @foreach($notfs as $notf)
        <li data-id="{{$notf->created_at}}" class="dropdown-content">
            @if(isset($notf->post))
                <p>{{$notf->emitter}} liked one of your posts <a href="{{ route('post', ['id' => $notf->post])}}">see</a></p>
                <button data-type="like_post" data-id="{{$notf->notfid}}">read</button>
            @endif
            @if(isset($notf->comment))
                <p>{{$notf->emitter}} commented on {{$notf->comment}}</p>
                <button data-type="comment" data-id="{{$notf->notfid}}">read</button>
            @endif
            @if(isset($notf->liked_comment))
                <p>{{$notf->emitter}}liked your comment {{$notf->liked_comment}}</p>
                <button data-type="like_comment" data-id="{{$notf->notfid}}">read</button>
            @endif
        </li>

    @endforeach
    </ol>

    
    
