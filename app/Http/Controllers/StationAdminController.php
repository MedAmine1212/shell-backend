<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Station;
use App\Models\StationAdmin;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;

class StationAdminController extends Controller
{
    public function addStationAdmin(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
        $user = $userController->addUser($request);
            if($user == "phone in use") {
                return response()->json(["Phone number already in use"],401);
            } else if ($user == "barcode in use") {
                return response()->json(["Barcode already assigned"],401);
            } else {
                return response()->json(["stationAdmin" => StationAdmin::create([
                    'user_id' => $user->id
                ])], 200);
            }
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAll(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["StationAdmins"=>StationAdmin::with('user')->get()], 200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function assignAdminToStation(Request $request, UserController $userController, $station_id, $station_admin_id) {
        if($userController->isSuperAdmin($request->user())) {
            $station = Station::where('id',$station_id)->get()->first();
            if(!$station) {
                return response()->json(["Station not found"],404);
            }
            if(StationAdmin::where('id',$station_admin_id)->get()->first()) {
            $station->station_admin_id = $station_admin_id;
            $station->update();
            return response()->json(["Station admin updated successfully"],200);
            }
            return response()->json(["Station admin not found"],404);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
