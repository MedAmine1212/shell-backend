<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\BorneAdvertisements;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{

    public function removeAdFromBorne(Request $request, UserController $userController ,$advertisement_id, $borne_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $bornAd = BorneAdvertisements::where("borne_id", $borne_id)->where("advertisement_id", $advertisement_id)->get()->first();
            if(!$bornAd)
                return response()->json(["Ad not affected to this borne"], 404);
            BorneAdvertisements::where("borne_id", $borne_id)->where("advertisement_id", $advertisement_id)->delete();
            return response()->json(["Ad removed from borne successfully"],200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
    public function addAdToBorne(Request $request, UserController $userController ,$advertisement_id, $borne_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $bornAd = BorneAdvertisements::where("borne_id", $borne_id)->where("advertisement_id", $advertisement_id)->get()->first();
            if($bornAd)
                return response()->json(["Ad already affected to this borne"], 200);
            BorneAdvertisements::create(["borne_id"=> $borne_id, "advertisement_id"=> $advertisement_id]);
            return response()->json(["advertisement added to borne successfully"],200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
    public function delete(Request $request, UserController $userController ,ProductController $productController, $advertisement_id) {
        if($userController->isSuperAdmin($request->user())) {
            $advertisement = Advertisement::where("id", $advertisement_id)->get()->first();
            if(!$advertisement)
                return response()->json("not found",404);
            $advertisement->delete();
            $productController->removeImage($advertisement->file, 'ads');
            return response()->json("advertisement deleted successfully",200);

        }else{
            return response()->json(["Forbidden"],403);
        }

        }
    public function createNewAdd(Request $request, UserController $userController ,ProductController $productController) {

        if($userController->isSuperAdmin($request->user())) {
            $fileName = $productController->saveImage($request->file("file"),'ads/');
            $advertisement = Advertisement::create([
                "title" => $request->get("title"),
                "isVideo" => filter_var($request->get("isVideo"), FILTER_VALIDATE_BOOLEAN),
                "file" => $fileName
            ]);
            return response()->json(["advertisement"=>$advertisement],200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
    public function findAllByBorneId(Request $request, UserController $userController ,$borne_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $borneAds = BorneAdvertisements:: where("borne_id", $borne_id)->with("advertisement")->get();
            $ads = [];
            if (sizeof($borneAds) > 0) {
                foreach ($borneAds as $ad) {
                    array_push($ads, $ad->advertisement);
                }
            }
                return response()->json(["advertisements" => $ads], 200);
            } else {
                return response()->json(["Forbidden"], 403);
            }
        }
    public function findAll(Request $request, UserController $userController) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["advertisements"=>Advertisement::all()],200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
