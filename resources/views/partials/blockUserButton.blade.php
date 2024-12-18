@if(Auth::user()->isAdmin() && $user->user_id !== Auth::user()->user_id)
    @if(DB::table('blocked')->where('blocked_id', $user->user_id)->exists())
        <button type="button" class="btn btn-success btn-lg mb-2 unblock-user" 
            data-unblock-url="{{ route('unblockUser', ['id' => $user->user_id]) }}"
            style="height:45px;">
            Unblock User
        </button>
    @else
        <button type="button" class="btn btn-danger btn-lg mb-2 block-user" 
            data-block-url="{{ route('blockUser', ['id' => $user->user_id]) }}"7
            style="height:45px">
            Block User
        </button>
    @endif
@endif
