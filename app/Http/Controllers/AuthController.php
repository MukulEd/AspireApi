<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {   
        $credentials = request(['email','password']);
        if(!(Auth::attempt($credentials)))
        {
            return response(['message'=>'Invalid Credentials'],422);
        }

        $user=User::where('email',$request->email)->first();
        $authToken=$user->createToken('aspire-auth-token')->plainTextToken;

        $response=[
            'user'=>$user,
            'access_token'=>$authToken,
            'token_type'=>'Bearer'
        ];

        return response($response,200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response(['message'=>'Logged Out'], 200);
    }



}
