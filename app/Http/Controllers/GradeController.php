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
            $user = User::find($request['user_id']);

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

                    $avg_grade = Grade::where('event_id','=',$event->id)->avg('grade');
                    $event->grade = $avg_grade;
                    $event->save();
                    return $this->message('success','Ok');
                }
                return $this->message('fail','vec ocjenio');
            }
            return $this->message('fail','ne postoji korisnik ili event');
        }
        catch(\Exception $e){
            return $this->message('fail','Something went wrong');
        }
    }
}
