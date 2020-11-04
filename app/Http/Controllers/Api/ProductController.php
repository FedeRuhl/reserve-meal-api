<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with('images')
            ->with('prices')
            ->get();
        dd($products);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'description' => 'min:10',
            'stock' => 'min:0'
        ]);

        $product = Product::create($request->all(['name', 'description', 'stock']));

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function getImages(Product $product){
        return $product->images()->get();
    }

    public function getPrices(Product $product){
        return $product->prices()->get();
    }
}
