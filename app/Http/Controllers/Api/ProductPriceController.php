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

        $productPrice = ProductPrice::create($request->all(['price', 'product_id']));

        return response()->json([
            'success' => true,
            'product' => $productPrice
        ]);
    }

    public function update(Request $request, ProductPrice $productPrice){
        $productPrice->date_until = date('Y-m-d H:i:s');
        $productPrice->save();

        $request['product_id'] = $productPrice->Product()
                                ->select('id')->first()['id'];
        return $this->store($request);
    }
}
