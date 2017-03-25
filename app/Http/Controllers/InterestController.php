<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class InterestController extends Controller
{
    public function interested(Request $request)
    {
        try{
            $user = User::find($request['user_id']);
            $user->interests()->attach($request['event_id']);
            return $this->message('success','Ok');
        }
        catch (\Exception $ex)
        {
            return $this->message('fail','Something went wrong');
        }

    }

    public function uninterested(Request $request)
    {
        $user = User::find($request['user_id']);
        $user->interests()->detach($request['event_id']);

        return $this->message('success','Ok');
    }
}
