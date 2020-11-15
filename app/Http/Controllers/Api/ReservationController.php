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
                'message' => "The product has been successfully updated",
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
}
