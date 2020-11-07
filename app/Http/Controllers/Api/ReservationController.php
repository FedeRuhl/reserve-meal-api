<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ProductPrice;
use App\Models\Product;
use Auth;
use Illuminate\Support\Facades\Gate;

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
        $request->validate([
            'scheduled_date' => 'required|date',
            'product_id' => 'required'
        ]);

        $productId = $request->product_id;
        $scheduledDate = $request->scheduled_date;
        $user = Auth::guard('api')->user();

        $product = Product::findOrFail($productId);

        $price = ProductPrice::select('price')
                ->where('product_id', $productId)
                //->where('date_until', '>', 'now')
                ->orderBy('date_until', 'desc')
                ->first()['price'];

        if ($product->stock > 0 
            && $user->amount >= $price) //stock maybe should be optional
        {
            $reservation = Reservation::create(
                [
                    'scheduled_date' => $scheduledDate,
                    'price' => $price,
                    'user_id' => $user->id,
                    'product_id' => $productId
                ]);
            
            $user->amount -= $price;
            $user->save();

            $product->stock -= 1;
            $product->save();

            return response()->json([
                'success' => true,
                'reservation' => $reservation
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
}
