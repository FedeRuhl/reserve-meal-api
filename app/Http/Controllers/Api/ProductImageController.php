<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductImage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ProductImageController extends Controller
{
    function index(){
        return ProductImage::all();
    }

    function store(Request $request){
        $request->validate([
            'product_id' => 'required',
            'product_image' => 'required|unique:product_images,product_image' //max:2048
        ]);

        $entry = $request->all();

        $name = $file->getClientOriginalName();
        $path = 'img/products';
        $file->move($path, $name);
        $image = $path."/".$name;
        $entry['product_image']=$image;

        $count = ProductImage::where('product_image', '=', $image)
            ->count();

        if ($count <= 1)
        {
            $productImage = ProductImage::create($entry);

            return response()->json([
                'success' => true,
                'image' => $productImage
            ]);
        }

        else
        {
            return response()->json([
                'success' => false,
                'error' => 'The image must be unique.'
            ]);
        }
        
    }
}