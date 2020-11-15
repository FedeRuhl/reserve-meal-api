<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductImage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    private $path = 'img/products';

    function index(){
        return ProductImage::all();
    }

    function store(Request $request){
        $request->validate([
            'product_id' => 'required',
            'product_image' => 'required|unique:product_images,product_image' //max:2048
        ]);

        
        $name = $request->file('product_image')
                ->getClientOriginalName();
        $fullPath = $this->path."/".$name;

        $count = $this->countProductsSamePhoto($fullPath);

        if ($count <= 1)
        {
            $request->file('product_image')
                ->move($this->path, $name);

            $productImage = ProductImage::create([
                'product_id' => $request->product_id,
                'product_image' => $fullPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'The image product has been successfully created',
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

    function update(Request $request, ProductImage $productImage){

        $request->validate([
            'product_image' => 'required|unique:product_images,product_image'
        ]);

        if (File::exists($productImage->product_image)){

            File::delete($productImage->product_image);

            $name = $request->file('product_image')
                ->getClientOriginalName();
            $fullPath = $this->path."/".$name;

            $request->file('product_image')
                ->move($this->path, $name);

            $productImage->product_image = $fullPath;
            $productImage->save();

            return response()->json([
                'success' => true,
                'message' => 'The image product has been successfully updated',
                'image' => $productImage
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'The image is not on our servers'
            ]);
        }

    }

    public function destroy(ProductImage $productImage)
    {
        $productImage->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'The product image has been successfully deleted'
        ]);
    }

    private function countProductsSamePhoto($image){
        return ProductImage::where('product_image', '=', $image)
            ->count();
    }
}