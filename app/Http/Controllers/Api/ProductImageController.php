<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    private $path = 'img/products';

    function index(){
        return ProductImage::all();
    }

    function store(Request $request){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'product_image' => 'required|unique:product_images,product_image' //max:2048
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        $name = $request->file('product_image')
                ->getClientOriginalName();
        $fullPath = $this->path."/".$name;

        $count = $this->countProductsSamePhoto($fullPath);

        if ($count <= 1)
        {
            $request->file('product_image')
                ->move($this->path, $name);

            try
            {
                $productImage = ProductImage::create([
                    'product_id' => $request->product_id,
                    'product_image' => $fullPath
                ]);
            }
            catch(\Illuminate\Database\QueryException $exception)
            {
                return response()->json([
                    'success' => false,
                    'message' => $exception->errorInfo[2] //message without sql info
                ]);
            }

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
                'message' => 'The image must be unique.'
            ]);
        }
        
    }

    function storeAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'size' => 'required'
        ]);

        $id = $request->product_id;
        $success = true;
        $errorMessage = '';

        for ($i = 0; $i < $request->size; $i++)
        {
            if ($request->hasFile('product_image_' . $i))
            {
                $data = new \Illuminate\Http\Request;
                $data = $data->createFromBase(\Symfony\Component\HttpFoundation\Request::create('/', 'POST', ['product_id' => $id], [], ['product_image' => $request->file('product_image_' . $i)]));

                $response = json_decode($this->store($data)->content());
                if(!$response->success)
                {
                    $success = false;
                    $errorMessage = $response->message;
                    break;
                }
            }
        }   

        if ($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if (!$success)
        {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'The images have been successfully created'
        ]);

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