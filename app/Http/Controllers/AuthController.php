<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{

    /**
     * Display the user profile.
     * @return \Illuminate\Http\Response
    */
    public function profile()
    {
        return response()->json(
            [
                'status' => true,
                'user'   => Auth::user()
            ]
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->token()->revoke();

        return response()->json([
            'status'=>true,
            'message' => 'Successfully logged out'
        ]);
    }

}
