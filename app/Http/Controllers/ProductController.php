<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function findAvailableProductsToAdd(Request $request, UserController $userController, $station_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $products = DB::table('products')
                ->whereNotIn('id', function($query) use ($station_id) {
                    $query->select('product_id')
                        ->from('product_stations')
                        ->where('station_id', '=', $station_id);
                })
                ->get();

            return response()->json(["products" => $products],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function getAllProducts(Request $request, UserController $userController) {
        if ($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["products"=>Product::all()],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function addProduct(Request $request, UserController $userController) {
        if ($userController->isSuperAdmin($request->user()) ) {
            $product = Product::where("label",$request->get("label"))->get()->first();
            if($product) {
                $product->stock+=$request->get("stock");
                $product->update();
                return response()->json(['type'=>"update","product"=>$product],200);
            } else {
                //save image
                $imageName = $this->saveImage($request->file("image"));

                $product = Product::create([
                    'label' => $request->get('label'),
                    'description' => $request->get('description'),
                    'image' =>$imageName,
                    'price' => $request->get('price'),
                    'stock' => $request->get('stock'),
                ]);
                return response()->json(['type'=>"new","product"=>$product],200)->header('Cache-Control', 'no-cache, no-store, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', '0');
            }
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function getImageByProductId($product_id) {
        $product = Product::where('id',$product_id)->get()->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
        $imagePath = public_path('images/products/' . $product->image);

        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = mime_content_type($imagePath);
        $image = 'data:image/' . $imageType . ';base64,' . $imageData;
        return response()->json(["image"=>$image],200)->header('Cache-Control', 'no-cache, no-store, must-revalidate')->header('Pragma', 'no-cache')->header('Expires', '0');
    }

    public function updateProduct(Request $request, UserController $userController, $product_id) {
        if ($userController->isSuperAdmin($request->user()) ) {
            $product = Product::where('id',$product_id)->get()->first();

            if(!$product)
                return response()->json([" product not found"],404);
                if($request->has("label")){
                    $pr = Product::where("label",$request->get("label"))->get()->first();

                    //check if label allready in use
                    if($pr){
                        if($pr->id != $product_id)
                        return response()->json(["Product name already in use"],403);
                    }
                    $product->label = $request->get('label');
                }
                if($request->has("description"))
                    $product->description = $request->get('description');
                if($request->has("price"))
                    $product->price = $request->get('price');
                if($request->has("stock"))
                    $product->stock = $request->get('stock');
                if($request->hasFile("image")) {
                    if($request->file("image")!= null) {
                        //remove old
                        $this->removeImage($product->image);
                        //save new
                        $product->image = $this->saveImage($request->file("image"));
                    }
                }
                $product->update();
                return response()->json(["Product updated successfully"],200);
            } else {
                return response()->json(["Forbidden"],403);
            }
    }

    public function saveImage($image) {
        $image_name = time() . '-' . $image->getClientOriginalName();
        $image->move(public_path('images/products/'), $image_name);
        return $image_name;
    }
    public function deleteProduct($product_id, UserController $userController, Request $request) {

        if ($userController->isSuperAdmin($request->user()) ) {
            $product = Product::where('id',$product_id)->get()->first();
            if(!$product)
                return response()->json(["Product not found"],404);
            $this->removeImage($product->image);
            $product->delete();
            return response()->json(["Product deleted successfully"],200);
        }else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function removeImage($imagePath) {
        $path = public_path('images\products\\' . $imagePath);
        unlink($path);
    }

    public function findAllByStationId($station_id) {
        return response()->json(["products"=>ProductStation::where("station_id", $station_id)->with("product")->get()],200);

    }
}
