<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Notifications\Notifiable;

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

        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect credentials.'
            ]);
        }
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function forgot(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email'
        ]);

        Password::sendResetLink($credentials);

        return response()->json([
            'success' => true,
            'message' => 'Reset password link sent on your email id.'
        ]);
    }

    public function reset(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'token' => 'required|string'
        ]);

        $email_password_status = Password::reset($credentials, function($user, $password){
            $user->password = bcrypt($password);
            $user->save();
        });

        if ($email_password_status == Password::INVALID_TOKEN)
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset password token.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password successfully changed.'
        ]);
    }
}