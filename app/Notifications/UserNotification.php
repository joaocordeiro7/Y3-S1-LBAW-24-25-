<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserNotification extends Notification
{
    use Queueable;

    protected datatime $created_at;
    protected int $emitter;
    protected int $post;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public static function readNotf(Request $request){
        
        if($request['type']==='like_post'){
            DB::table('upvoteonpostnotification')->where('notfid',$request['id'])->update(['is_read'=>true]);
        }

        if($request['type']==='like_comment'){
            DB::table('upvoteoncommentnotification')->where('notfid',$request['id'])->update(['is_read'=>true]);
        }

        if($request['type']==='comment'){
            DB::table('commentnotification')->where('notfid',$request['id'])->update(['is_read'=>true]);
        }


    }

    public static function getNotif(){
        $postNotifications = DB::table('upvoteonpostnotification')->where('receiver', Auth::id())->where('is_read',False)->orderBy('created_at','desc')->get();
        $upvoteCommentNotifications = DB::table('upvoteoncommentnotification')->where('receiver', Auth::id())->where('is_read',False)->orderBy('created_at','desc')->get();
        $commentNotifications = DB::table('commentnotification')->where('receiver', Auth::id())->where('is_read',False)->orderBy('created_at','desc')->get();
        $notifications = $postNotifications->merge($upvoteCommentNotifications)->merge($commentNotifications);
        return $notifications->sortByDesc('created_at');
    }


    public static function getNewNotifs(Request $request){
        $newPostNotif =  DB::table('upvoteonpostnotification')
                            ->where('receiver', Auth::id())
                            ->where('is_read',False)
                            ->where('created_at','>',$request->input('lastId'))
                            ->orderBy('created_at','desc')->get();

        $newCommentNotif =  DB::table('commentnotification')
                            ->where('receiver', Auth::id())
                            ->where('is_read',False)
                            ->where('created_at','>',$request->input('lastId'))
                            ->orderBy('created_at','desc')->get();

        $newLikedCommentNotif =  DB::table('upvoteoncommentnotification')
                            ->where('receiver', Auth::id())
                            ->where('is_read',False)
                            ->where('created_at','>',$request->input('lastId'))
                            ->orderBy('created_at','desc')->get();

        $newNotfs=$newPostNotif->merge($newCommentNotif)->merge($newLikedCommentNotif);
        
        return $newNotfs->sortByDesc('created_at');
    }


    
}
