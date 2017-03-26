<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Event;
use App\User;

class CommentController extends Controller
{
    public function index($eventId)
    {
        $comments = Comment::where('event_id','=',$eventId)->get();
        foreach($comments as $comment){
            $comment->user_name = User::find($comment->user_id)->name;
        }
        return $comments;
    }

    public function store(Request $request){
        try{
            $event = Event::find($request['event_id']);
            $user = User::find($request['user_id']);
            $comment = Comment::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'comment' => $request['comment']
            ]);

            return $this->message('success','Ok');
        }
        catch (\Exception $e){
            return $this->message('fail','Something went wrong');
        }

    }
}
