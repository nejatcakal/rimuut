<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
  
     * @param  String type
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, String $type){
         // The user type get from url and check it
        $all_input =  $request->all();
        $all_input["type"] = $type;
        $validator = Validator::make(
            $all_input,
            [
                'name'=> 'required|string',
                'surname'=> 'required|string',
                'email'=> 'required|email|unique:users',
                'password' => 'required|confirmed|min:6', 
                'address' => 'required|string',
                'type' => 'required|string|in:freelancer,business',
                
            ],
            [
                'type.in' => 'The URL must include freelancer or business'
            ]
        );    

        if($validator->fails()){
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors()
                ],
                400
            );
        }



        $user = User::create([
            'name'=>$request->name,
            'surname'=>$request->surname,
            'email'=>$request->email,
            'address'=>$request->address,
            'type'=>$type,
            'password' => bcrypt($request->password)
        ]);
    
        //User Authentication create, set token
        $credentials = ['email'=>$request->email,'password'=>$request->password,'type'=>$type];

        if(!Auth::attempt($credentials)){
            return response()->json([
                'status'  => false,
                'message' => 'Oops, You have to check informations!',
            ],401);
        }
     
        $accessToken = $user->createToken('Personal Access')->accessToken;
       
        return response()->json([
            'status'=>true,
            'user'=>Auth::user(),
            'access_token'=>$accessToken,
            'token_type'=>'Bearer',
        ],201);
        
    }
}
