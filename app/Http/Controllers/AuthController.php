<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=>'Invalid Login Details'
            ],401);
        }

        $user = User::where('email',$request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;
       

        return response()->json([
            'user'=> new UserResource($request->user()),
            'access_token'=>$token,
            'token_type'=>"Bearer"
        ]);


    }

    public function test(Request $request)
    {
        return new UserResource($request->user());
    }
}
