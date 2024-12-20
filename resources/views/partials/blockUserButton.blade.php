@if(Auth::user()->isAdmin() && $user->user_id !== Auth::user()->user_id)
    @if(DB::table('blocked')->where('blocked_id', $user->user_id)->exists())
        <button type="button" class="block-unblock btn btn-success btn-lg unblock-user" 
            data-unblock-url="{{ route('unblockUser', ['id' => $user->user_id]) }}">
            Unblock User
        </button>
    @else
        <button type="button" class="block-unblock btn btn-danger btn-lg block-user" 
            data-block-url="{{ route('blockUser', ['id' => $user->user_id]) }}">
            Block User
        </button>
    @endif
@endif
