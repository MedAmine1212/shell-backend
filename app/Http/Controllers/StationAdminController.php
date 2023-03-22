<?php

namespace App\Http\Controllers;
use App\Models\Station;
use App\Models\StationAdmin;
use Illuminate\Http\Request;

class StationAdminController extends Controller
{

    public function getStationAdminsWithNoStation() {
        return response()->json(["stationAdmins"=>StationAdmin::whereDoesntHave('station')->with("user")->get()],200);
    }

    public function addStationAdmin(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            if(!$request->has("password"))
            {
                return response()->json(["Password required"],401);
            }
        $user = $userController->addUser($request, false);
            if($user == "password required") {
                return response()->json(["Password required"],401);
            }else if($user == "email in use") {
                return response()->json(["Email already in use"],401);
            } else if($user == "phone in use") {
                return response()->json(["Phone number already in use"],401);
            } else if ($user == "barcode in use") {
                return response()->json(["Barcode already assigned"],401);
            } else {
                $stationAdmin = StationAdmin::create([
                    'user_id' => $user->id
                ]);
                $stationAdmin->user = $user;
                return response()->json(["stationAdmin" => $stationAdmin], 200);
            }
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAll(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["stationAdmins"=>StationAdmin::with('user')->with("station")->get()], 200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function unassignStationAdmin(Request $request, UserController $userController, $station_id) {
        if($userController->isSuperAdmin($request->user())) {
            $station = Station::where('id',$station_id)->get()->first();
            if(!$station) {
                return response()->json(["Station not found"],404);
            }
            $station->station_admin_id = null;
            $station->save();
            return response()->json(["Station admin removed"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function assignAdminToStation(Request $request, UserController $userController, $station_admin_id, $station_id) {
        if($userController->isSuperAdmin($request->user())) {
            $station = Station::where('id',$station_id)->get()->first();
            if(!$station) {
                return response()->json(["Station not found"],404);
            }
            if(StationAdmin::where('id',$station_admin_id)->get()->first()) {
            $station->station_admin_id = $station_admin_id;
            $station->save();
            return response()->json(["Station admin updated successfully"],200);
            }
            return response()->json(["Station admin not found"],404);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
