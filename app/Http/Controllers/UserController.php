<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$users=User::all();
        $users = DB::table('users')->select('id','name', 'email as user_email')->get();
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mail = $request['email'];
        $user = User::where('email',$mail)->first();
        if(! $user) {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]);
            if ($user->save()) {
                $message = ["status" => "success", "message" => "Ok"];
                return $message;
            }
            else{
                $message = ["status"=>"failed","message"=>"something went wrong"];
                return $message;
            }
        }
        else{
            $message = ["status"=>"failed","message"=>"this email is already in use"];
            return $message;
        }
    }

    public function login(Request $request){
        $mail = $request['email'];
        $password = $request['password'];
        $user = User::where('email','=',$mail)->first();
        if($user) {
            if (Hash::check($password, $user['password'])) {
                $message = ["status" => "success", "message" => $user['name']];
                return $message;
            }
            /*wrong password*/
            else{
                $message = ['status'=>'failed', 'message'=>'invalid user credentials'];
                return $message;
            }
        }
        /*wrong mail*/
        else{
            $message = ['status'=>'failed', 'message'=>'invalid user credentials'];
            return $message;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $numberOfDelete=User::destroy($id);
        if($numberOfDelete==1){
            $message = ["status" => "success", "message" => "Ok"];
            return $message;
        }
        else{
            $message = ["status" => "failed", "message" => "something went wrong"];
            return $message;
        }

    }
}
