<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Response;
use App\User;
use App\Event;
use App\Grade;
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
            $event->email = User::getEmail($event->user_id);
            $event->comments = $event->comments->count();
        }

        return $events;
    }

    public function show($id, $email)
    {
        $event = Event::find($id);
        $creator = User::find($event['user_id']);
        $user = User::where('email','=',$email)->first();
        $event->interested = false;
        foreach($user->interests as $interest){
          if($interest->id == $id){
            $event->interested = true;
          }
        }

        $event['email'] = User::getEmail($creator->id);
        $event['name'] = $creator->name;
        return $event;
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        if ($validated != 1) {
            return $validated;
        } else {            
            $userId = User::getId($request['email']);
            $event = Event::create([
                'title' => $request['title'],
                'short_description' => $request['short_description'],
                'long_description' => $request['long_description'],
                'user_id' => $userId,
                'date_and_time_start' => $request['date_and_time_start'],
                'date_and_time_end' => $request['date_and_time_end'],
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
        try{
            $event = Event::find($id);
            $user = User::where('email','=',$request['email'])->first();
            if($event->user_id == $user->id || $user->role == "Admin"){
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
            return $this->message("failed", "You can not edit this event");
        }
        catch(\Exception $e){
            return $this->message("failed", "something went wrong");
        }

    }

    public function destroy($id, $email)
    {
        try{
            $user = User::where('email','=',$email)->first();
            $event = Event::find($id);
            if($event->user_id == $user->id || $user->role == "Admin"){
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
        catch (\Exception $e){
            return $this->message("failed","Something went wrong");
        }


    }

    public static function updateGrade($id)
    {
        try{
            $avg_grade = Grade::where('event_id','=',$id)->avg('grade');
            $event = Event::find($id);
            $event->grade = $avg_grade;
            $event->save();
            return true;
        }
        catch(\Exception $ex){
            return false;
        }

    }

    private function storePhoto($id, $request)
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

    private function validateEvent($request)
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
