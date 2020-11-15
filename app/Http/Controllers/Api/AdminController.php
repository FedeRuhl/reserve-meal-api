<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function addFunds(Request $request, User $user){ //user_id, amount

        $response = Gate::inspect('isAdmin');

        if($response->allowed()){
            $request->validate([
                'amount' => 'required|numeric'
            ]);
    
            $user->amount += $request->amount;        
            $user->save();
    
            return response()->json([
                'success' => true,
                'message' => 'The user has successfully received the funds.',
                'user' => $user
            ]);
        }

        else{
            return response()->json([
                'success' => false,
                'message' => $response->message()
            ]);
        }
        
    }

    public function destroy(User $user)
    {
        $response = Gate::inspect('isAdmin');

        if($response->allowed())
        {
            $name = $user->name;
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => "The user $name has been successfully deleted"
            ]);
        }

        else
        {
            return response()->json([
                'success' => false,
                'message' => $response->message()
            ]);
        }
        
    }
}
