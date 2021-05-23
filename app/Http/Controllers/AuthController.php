<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {   
        $credentials = request(['email','password']);
        $user=User::where('email',$request->email)->where('status',1)->first();
        
        if(empty($user) || !(Auth::attempt($credentials)))
        {
            return response(['message'=>'Invalid Credentials'],422);
        }

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
        try{
            $request->user()->tokens()->delete();
            return response(['message'=>'Logged Out'], 200);
            
        }catch(Exception $e)
        {
            $error=['message'=>'Unauthorized'];
            return response($error,422);
        }
    }



}
