<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Requests\UserRequest;
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
        $validated=$this->validateUser($request); 
        
        if($validated != 1){
            return $validated;
        }
        else{
            $mail = $request['email'];
            $user = User::where('email',$mail)->first();
            if(! $user) {
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => bcrypt($request['password']),
                ]);
                if ($user->save()) {                
                    return $this->message("success","Ok");
                }
                else{
                    return $this->message("failed","something went wrong");
                }
            }
            else{
                return $this->message("failed","this email is already in use");
            }
        }       
    }

    public function login(Request $request){
        $mail = $request['email'];
        $password = $request['password'];
        $user = User::where('email','=',$mail)->first();
        if($user) {
            if (Hash::check($password, $user['password'])) {
                return $user;
            }
            /*wrong password*/
            else{                
                return $this->message("failed","invalid user credentials");
            }
        }
        /*wrong mail*/
        else{            
            return $this->message("failed","invalid user credentials");
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
            return $this->message("success","Ok");
        }
        else{            
            return $this->message("failed","something went wrong");
        }
    }

    public function promote($id)
    {
        $user = User::find($id);
        $user->role="Admin";
        if($user->save()){
            return $this->message("Success","Ok");
        }
        else{
            return $this->message("Failed","Something went wrong");
        }
    }
    
    public function validateUser($request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:60',        
        ]);
        if ($validator->fails()) {
            return $this->message("failed","email is required");
        }

        $validator = Validator::make($request->all(), [        
            'name' => 'required',        
        ]);        
        if ($validator->fails()) {
            return $this->message("failed","name is required");
        }

        $validator = Validator::make($request->all(),
        [
            'password'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->message("failed","password is required");
        }
        
        return 1;
        

    }
}
