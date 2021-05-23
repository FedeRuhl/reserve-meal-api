<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        return Product::orderBy('id', 'desc')
            ->with('images')
            ->with('prices')
            ->get();
    }

    public function store(Request $request){
        $response = Gate::inspect('isAdmin');

        if($response->allowed()){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'min:10',
                'stock' => 'min:0'
            ]);
    
            if ($validator->fails())
            {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }
    
            try
            {
                $product = Product::create($request->all(['name', 'description', 'stock']));
                return response()->json([
                    'success' => true,
                    'message' => 'The product has been successfully created',
                    'product' => $product
                ]);
            }
    
            catch(\Illuminate\Database\QueryException $exception)
            {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ]);
            }
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => $response->message()
            ]);
        }
    }

    public function update(Request $request, Product $product){

        $request->validate([
            'name' => 'min:5|max:50',
            'description' => 'min:10|max:50',
            'stock' => 'min:0'
        ]);

        $product->name = ($request->input('name')) ? $request->input('name') : $product->name;
        $product->description = ($request->input('description')) ? $request->input('description') : $product->description;
        $product->stock = ($request->input('stock')) ? $request->input('stock') : $product->stock;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'The product has been successfully updated',
            'product' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'The product has been successfully deleted'
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
