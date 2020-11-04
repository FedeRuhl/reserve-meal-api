<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\ProductPrice;

class ProductPriceController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'price' => 'required|numeric',
            'product_id' => 'required'
        ]);

        $productPrice = ProductPrice::create($request->all(['date_until', 'price', 'product_id']));

        return response()->json([
            'success' => true,
            'product' => $productPrice
        ]);
    }
}
