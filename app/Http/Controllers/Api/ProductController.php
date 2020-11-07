<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(){
        return Product::with('images')
            ->with('prices')
            ->get();
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

    public function update(Request $request, Product $product){

        $request->validate([
            'name' => 'min:5|max:50',
            'description' => 'min:10|max:50',
            'stock' => 'min:0'
        ]);

        $product->name = $request->input('name'); //$request->name
        $product->description = $request->input('description');
        $product->stock = $request->input('stock');
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'The product has been successfully updated'
        ]);
    }

    public function getImages(Product $product){
        return $product->images()->get();
    }

    public function getActualPrice(Product $product){
        $now = Carbon::now()->toDateTimeString();
        return $product->prices()
            ->where('date_until', '>', $now)->get();
    }
}
