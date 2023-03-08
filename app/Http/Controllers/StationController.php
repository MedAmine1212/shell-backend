<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationAdmin;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class StationController extends Controller
{
    function findById($station_id) {
        $station = Station::where('id',$station_id)->with("stationAdmin.user")->with("workSchedule")->get()->first();
        if(!$station) {
            return response()->json(["Station not found"],404);
        }
        return response()->json(["station"=>$station],200);
    }
    function addStation(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            $stationAdmin = null;
            $workSchedule = null;
            if($request->has('station_admin_id'))
                $stationAdmin = $request->get("station_admin_id");
            if($request->has("work_schedule_id"))
                $workSchedule = $request->get("work_schedule_id");
            return response()->json(["Station" => Station::create([
                'label' => $request->get('label'),
                'address' => $request->get('address'),
                'station_admin_id' => $stationAdmin,
                'work_schedule_id' => $workSchedule
            ])],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    function getAllStations(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["Stations" => Station::with("stationAdmin.user")->get()],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    function getStationByAdminId($station_admin_id, Request $request, UserController $userController) {
        $station_admin = StationAdmin::where('id',$station_admin_id)->with("user")->get()->first();
        if(!$station_admin)
            return response()->json(["Station admin not found"],404);
        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($station_admin->user)) {
            $station = Station::where("station_admin_id", $station_admin_id)->with("employees.user")->get()->first();
            if($station) {
                return response()->json(["Station: "=>$station],403);
            }
            return response()->json("Station not found",404);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function update($station_id, Request $request, UserController $userController)
    {
        $station = Station::where('id',$station_id)->with("stationAdmin")->get()->first();
        if(!$station)
            return response()->json(["Station not found"],404);
        if ($station->stationAdmin->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {

            if($request->has("label"))
                $station->label = $request->get('label');
            if($request->has("address"))
                $station->address = $request->get('address');
            $station->update();

            return response()->json(["Station updated successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function delete($station_id, Request $request, UserController $userController) {
        if ($userController->isSuperAdmin($request->user())) {
            $station = Station::where('id',$station_id)->get()->first();
            if($station) {
                $station->delete();
                return response()->json(["Vehicle removed successfully"],200);
            }
            return response()->json(["Station not found"],404);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

}
