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

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user = UserResource::make($user);


        return response()->json([
            'user'=> $user,
            'access_token'=>$token,
            'token_type'=>"Bearer"
        ]);


    }

    public function test(Request $request)
    {
        return new UserResource($request->user());
    }
}
