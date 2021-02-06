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
            'message' => 'The product price has been successfully created',
            'product_price' => $productPrice
        ]);
    }

    public function update(Request $request, ProductPrice $productPrice){
        $productPrice->date_until = date('Y-m-d H:i:s');
        $productPrice->save();

        $request['product_id'] = $productPrice->Product()
                                ->select('id')->first()['id'];

        $this->store($request);

        return response()->json([
            'success' => true,
            'message' => 'The product price has been successfully updated',
            'product_price' => $request->price
        ]);
    }

    public function destroy(ProductPrice $productPrice)
    {
        $delete = $productPrice->delete();  
        
        return response()->json([
            'success' => true,
            'message' => 'The product price has been successfully deleted'
        ]);
    }
}
