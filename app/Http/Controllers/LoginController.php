<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * register.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  String type
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, String $type){
        $all_input =  $request->all();
        $all_input["type"] = $type;
        $validator = Validator::make(
            $all_input,
            [
                'email'=> 'required|email|exists:users',
                'password' => 'required|min:6', 
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

        $credentials = ['email'=>$request->email,'password'=>$request->password,'type'=>$type];

        if(!Auth::attempt($credentials)){
            return response()->json([
                'status'  => false,
                'message' => 'Oops, You have to check informations!',
            ],401);
        }
        $user = Auth::user();
        $accessToken = $user->createToken('Personal Access')->accessToken;
       
        return response()->json([
            'status'=>true,
            'user'=>Auth::user(),
            'access_token'=>$accessToken,
            'token_type'=>'Bearer',
        ],201);
        
    }
}
