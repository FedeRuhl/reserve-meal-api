<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required|min:6',
            'dni' => 'required|min:6'
        ]);


        $user = User::create([
            'name' => $request->name, 
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'dni' => $request->dni
        ]);

        return response()->json($user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', 
            'password' => 'required'
        ]);

        if( Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {
            $user = Auth::user();

            $token = $user->createToken($user->email.'-'.now());

            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);

            return response()->json([
                //'user' => $user,
                'jwt' => $token->accessToken
            ]);
        }
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}