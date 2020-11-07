<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use App\Models\User;

class UserController extends Controller
{
    public function show(){
        //return Auth::user();
        return Auth::guard('api')->user();
    }

    public function update(Request $request){

        $request->validate([
            'name' => 'min:5|max:30',
            'user_id' => 'min:6|max:10',
            'email' => 'email'
        ]);

        $user = Auth::guard('api')->user();
        $user->name = $request->input('name'); //$request->name
        $user->dni = $request->input('dni');
        $user->email = $request->input('email');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'The user has been successfully updated'
        ]);
    }
}
