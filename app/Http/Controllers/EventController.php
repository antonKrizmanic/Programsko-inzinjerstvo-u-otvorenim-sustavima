<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\Event;

class EventController extends Controller
{
    public function index(){
        $events = DB::table('events')->select('id','title','short_description','user_id')->get();

        foreach($events as $event ){
            $event->user_email=User::getEmail($event->user_id);            ;
        }
        return $events;
    }
    public function show($id){
        $event = Event::find($id);
        $event['user_mail']=User::getEmail($event['user_id']);
        return $event;
    }

    public function store(Request $request){
        $userId=User::getId($request['user_email']);
        $event = Event::create([
            'title' => $request['title'],
            'short_description' => $request['short_description'],
            'long_description' => $request['long_description'],
            'user_id'=>$userId,
        ]);
        if ($event->save()) {
            $message = ["status" => "success", "message" => "Ok"];
            return $message;
        }
        else{
            $message = ["status"=>"failed","message"=>"something went wrong"];
            return $message;
        }
    }
}
