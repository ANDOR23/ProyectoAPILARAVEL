<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //CREAR UNA INSTANCIA DE AuthController

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(){
        $credentials = request(['email', 'password']);
        if(! $token = auth()->attempt($credentials)){
            return response ()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function me(){
        return response()->json(auth()->user());
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Successfily logged out']);
    }

    public function refresh(){
        return $this->respondWithToken(auth()->refresh());
    }

    public function respondWithToken($token){
        return response()->json([
            'access_token' =>$token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
