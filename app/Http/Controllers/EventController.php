<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Response;
use App\User;
use App\Event;
use File;
use Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        $events = DB::table('events')->select('id', 'title', 'short_description', 'photo', 'user_id')
                                    ->where('deleted_at','=',null)
                                    ->get();

        foreach ($events as $event) {
            $event->user_email = User::getEmail($event->user_id);
        }

        return $events;
    }

    public function show($id)
    {
        $event = Event::find($id);
        $event['user_mail'] = User::getEmail($event['user_id']);
        dd($event->interested);
        return $event;
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        if ($validated != 1) {
            return $validated;
        } else {
            $userId = User::getId($request['user_email']);
            $event = Event::create([
                'title' => $request['title'],
                'short_description' => $request['short_description'],
                'long_description' => $request['long_description'],
                'user_id' => $userId,
            ]);
            if ($event->save()) {
                //if event is created find that event and attach photo with name in format "id-yyyy-mm-dd.jpg" and return JSON object from thah function
                return $this->storePhoto($event->id, $request);
            } else {
                return $this->message("failed", "something went wrong");
            }
        }
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $userId = User::getId($request['user_email']);
        if($event->user_id != $userId){
            return $this->message("failed", "You can not edit this event");
        }
        $validated = $this->validateEvent($request);

        if ($validated != 1) {
            return $validated;
        } else {


            $event->title = $request['title'];
            $event->short_description = $request['short_description'];
            $event->long_description = $request['long_description'];

            if ($event->save()) {
                return $this->storePhoto($event->id, $request);
            } else {
                return $this->message("failed", "something went wrong");
            }
        }
    }

    public function destroy($id, $userMail)
    {
        $userId = User::getId($userMail);
        $event = Event::find($id);
        if($event->user_id == $userId){
            $numberOfDelete=Event::destroy($id);
            if($numberOfDelete==1){
                return $this->message("success","Ok");
            }
            else{
                return $this->message("failed","Something went wrong");
            }
        }
        else{
            return $this->message("failed","You are not author of this event!");
        }

    }

    public function storePhoto($id, $request)
    {
        $event = Event::find($id);
        if ($request->hasFile('photo')) {
            $filename = $request->file('photo')->store('/event','s3');
            $path = Storage::cloud()->url($filename);
        }
        else if($event->photo != ""){
            $path = $event->photo;
        }
        else {
            $path = "";
        }
        $event->photo = $path;
        if ($event->save()) {
            return $this->message("success", "Ok");
        } else {
            return $this->message("success", "Event is created but photo isn't saved");
        }
    }

    public function validateEvent($request)
    {
        $validator = Validator::make($request->all(), [
            'short_description' => 'max:140',
        ]);
        if ($validator->fails()) {
            return $this->message("failed", "Short description is too long.");
        }
        return 1;
    }
}
