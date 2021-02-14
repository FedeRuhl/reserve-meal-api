<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ProductPrice;
use App\Models\Product;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function index(){
        if (Gate::allows('isAdmin')){
            return response()->json(
                Reservation::orderBy('scheduled_date')
                    ->get()
            );
        }
        else
        {
            $userId = Auth::guard('api')->id();
            return response()->json(
                Reservation::where('user_id', $userId)
                    ->orderBy('scheduled_date')
                    ->get()
            );
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_date' => 'required|date',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = Auth::guard('api')->user();
        $product = Product::findOrFail($request->product_id);

        $price = ProductPrice::select('price')
                ->where('product_id', $request->product_id)
                //->where('date_until', '>', 'now')
                ->orderBy('date_until', 'desc')
                ->first()['price'];

        if ($product->stock > $request->quantity 
            && $user->amount >= $request->amount)
        {
            if ($price * $request->quantity != $request->amount)
            {
                return response()->json([
                    'success' => false,
                    'message' => "The submitted data is not matching with our data. Please, try again later"
                ]);
            }
            $reservation = Reservation::create(
                [
                    'scheduled_date' => $request->scheduled_date,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'amount' => $request->amount,
                    'user_id' => $user->id,
                ]);
            
            $user->amount -= $request->amount;
            $user->save();

            $product->stock -= $request->quantity;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => "The order has been successfully created"
            ]);
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => "The product is not in stock, or user does not have sufficient funds"
            ]);
        }
        
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'scheduled_date' => 'date',
            'product_id' => 'integer'
        ]);

        $reservation->scheduled_date = ($request->input('scheduled_date')) ? $request->input('scheduled_date') : $reservation->scheduled_date;
        $reservation->product_id = ($request->input('product_id')) ? $request->input('product_id') : $reservation->product_id;
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'The reservation has been successfully updated',
            'reservation' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $delete = $reservation->delete();  
        
        return response()->json([
            'success' => true,
            'message' => 'The reservation has been successfully deleted'
        ]);
    }

    public function getByUser()
    {
        $user = Auth::guard('api')->user();
        $reserves = Reservation::where('user_id', $user->id)
            ->with('product')
            ->get();

        if ($reserves)
        {
            return response()->json([
                'success' => true,
                'message' => 'The user has been successfully found',
                'reserves' => $reserves
            ]);
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'The user is not found',
                'reserves' => []
            ]);
        }
    }
}
