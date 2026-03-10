<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'email' => ['required' , 'string'  , 'max:255'],
            'name' => ['required' , 'string'  , 'max:255'],
            'password' => ['required' , 'string'  , 'min:8'],
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => 'reader',
            'password' => Hash::make($request->input('password'))
        ]);
        $user->save();


        return response()->json([
            'message' => 'User created successfully',
            'user' => $user 
        ],201);
    }
    public function login(Request $request){
        $request->validate([
            'email' => ['required' , 'string'  , 'max:255'],
            'password' => ['required' , 'string'  , 'min:8'],
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if (!$user || !Hash::check($request->input('password'), $user->password)){
            throw ValidationException::withMessages([
                'message'=>'Invalid Credential'
            ]);
        }
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user ,
            'token' => $token
        ]);
    }
    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    public function me(Request $request){
        
        return response()->json([
            'user'=>$request->user()
        ]);
    }
}
