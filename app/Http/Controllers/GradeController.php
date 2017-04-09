<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\User;
use App\Grade;

class GradeController extends Controller
{
    public function store(Request $request)
    {
        try{
            $event = Event::find($request['event_id']);
            $user = User::where('email','=',$request['email'])->first();

            if($event != null && $user != null){
                $checkGrade = Grade::where([
                    ['event_id','=',$event->id],
                    ['user_id','=',$user->id]
                ])->count();

                if($checkGrade == 0){
                    $grade = Grade::create([
                        'user_id' => $user->id,
                        'event_id' =>$event->id,
                        'grade' =>$request['grade']
                    ]);

                    EventController::updateGrade($event->id);
                    return $this->message('success','Ok');
                }
                return $this->message('fail','You allready give this event grade');
            }
            return $this->message('fail','Somethnig went wrong');
        }
        catch(\Exception $e){
            return $this->message('fail','Something went wrong');
        }
    }
}
