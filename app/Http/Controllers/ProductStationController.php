<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStation;
use App\Models\Station;
use Illuminate\Http\Request;

class ProductStationController extends Controller
{
    public function addProductToStation(Request $request, UserController $userController, $product_id, $station_id) {
        $station = Station::where('id',$station_id)->with("stationAdmin")->get()->first();

        if(!$station)
            return response()->json(["station not found"],404);
        if(($userController->isStationAdmin($request->user()) && $station->stationAdmin->user_id == $request->user()->id) || $userController->isSuperAdmin($request->user())) {
            $product = Product::where('id',$product_id)->get()->first();
            if(!$product)
                return response()->json(["product not found"],404);

            if($request->get("stock") > $product->stock)
                return response()->json(["Insufficient product stock"],403);
            $productStation = ProductStation::where("product_id",$product_id)->where("station_id",$station_id)->get()->first();

            if($productStation){
                ProductStation::where("product_id",$product_id)->where("station_id",$station_id)->update(["stock" =>$productStation->stock+=$request->get("stock")]);
            } else {
            $productStation = ProductStation::create([
                "product_id" =>$product_id,
                "station_id" =>$station_id,
                "price" => $request->get("price"),
                "stock" => $request->get("stock")
            ]);
            }
            $product = $this->adjustStock($product_id, $request->get("stock")*(-1));
            $productStation->product = $product;
            return response()->json(["productStation"=>$productStation],200);

        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function removeProductFromStation(Request $request, UserController $userController, $product_id, $station_id, $restoreStock) {

        $station = Station::where('id',$station_id)->with("stationAdmin")->get()->first();

        if(!$station)
            return response()->json(["station not found"],404);
        if(($userController->isStationAdmin($request->user()) && $station->stationAdmin->user_id == $request->user()->id) || $userController->isSuperAdmin($request->user())) {
            $product = Product::where('id',$product_id)->get()->first();
            if (!$product)
                return response()->json(["product not found"], 404);
            $productStation = ProductStation::where("product_id",$product_id)->where("station_id",$station_id)->get()->first();
            if(!$productStation)
                return response()->json(["product not affected to station"], 404);

            if($restoreStock == 1) {
                $this->adjustStock($product_id,$productStation->stock);
            }
            ProductStation::where("product_id",$product_id)->where("station_id",$station_id)->delete();

            return response()->json(["Product removed from station successfully"],200);

        }else {
            return response()->json(["Forbidden"],403);
        }
        }

    public function adjustStock($product_id, $stock) {
        $product = Product::where('id',$product_id)->get()->first();
        if(!$product)
            return response()->json(["Product not found"],404);
        $product->stock+=$stock;
        $product->update();
        return  $product;
    }
}
