
    <ol id="notfs" class="dropdown-content">
    @foreach($notfs as $notf)
        <li data-id="{{$notf->created_at}}" class="dropdown-content">
            @if(isset($notf->post))
                <p>{{App\Models\User::find($notf->emitter)->username}} liked one of your posts <a href="{{ route('post', ['id' => $notf->post])}}">see</a></p>
                <button data-type="like_post" data-id="{{$notf->notfid}}">
                    <i class="fa-solid fa-check"></i>
                </button>
            @endif
            @if(isset($notf->comment))
                <p>{{App\Models\User::find($notf->emitter)->username}} commented on one of your posts</p>
                <button data-type="comment" data-id="{{$notf->notfid}}">
                    <i class="fa-solid fa-check"></i>
                </button>
            @endif
            @if(isset($notf->liked_comment))
                <p>{{App\Models\User::find($notf->emitter)->username}} liked one of your comments</p>
                <button data-type="like_comment" data-id="{{$notf->notfid}}">
                    <i class="fa-solid fa-check"></i>
                </button>
            @endif
        </li>

    @endforeach
    </ol>

    
    
