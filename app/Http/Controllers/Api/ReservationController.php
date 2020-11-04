<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'scheduled_date' => 'required|date',
            'user_id' => 'required',
            'product_id' => 'required'
        ]);

        $reservation = Reservation::create($request->all(['scheduled_date', 'user_id', 'product_id']));

        return response()->json([
            'success' => true,
            'reservation' => $reservation
        ]);
    }
}
