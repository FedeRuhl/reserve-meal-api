<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Notifications\Notifiable;
use App\Mail\EmailDemo;
use Illuminate\Support\Facades\Mail;
use Str;

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
            'dni' => 'required|exists:users,dni', 
            'password' => 'required'
        ]);

        if( Auth::attempt(['dni'=>$request->dni, 'password'=>$request->password]) ) {
            $user = Auth::user();

            $token = $user->createToken($user->email.'-'.now());

            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);

            return response()->json([
                //'user' => $user,
                'success' => true,
                'jwt' => $token->accessToken
            ]);
        }

        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect credentials'
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
            'email' => 'required|email|exists:users,email'
        ]);

        $code = Str::random(6);
        Mail::to($request->email)->send(new EmailDemo($code));

        $user = User::where('email', $credentials['email'])->first();
        if (!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'The user does not exist',
            ]);
        }

        $user->verification_code = $code;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Reset password link sent on your email'
        ]);
    }

    public function reset(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'The user does not exist',
            ]);
        }

        $code = $user->verification_code;

        if ($credentials['code'] != $code)
        {
            return response()->json([
                'success' => false,
                'message' => "Verification code is incorrect"
            ]);
        }

        if ($credentials['password'] != $credentials['password_confirmation'])
        {
            return response()->json([
                'success' => false,
                'message' => "Passwords don't match."
            ]);
        }
        
        $user->password = bcrypt($credentials['password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password successfully changed.'
        ]);
    }
}