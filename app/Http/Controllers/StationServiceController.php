<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Station;
use App\Models\StationService;
use Illuminate\Http\Request;

class StationServiceController extends Controller
{
    public function removeServiceFromStation(Request $request, UserController $userController, $service_id, $station_id) {
        $stationService = StationService::where("service_id",$service_id)->where("station_id",$station_id)->get()->first();
        if(!$stationService)
            return response()->json(["service not affected to station"],404);
        $station = Station::where('id',$station_id)->with("stationAdmin")->get()->first();
        if(!$station)
            return response()->json(["station not found"],404);

        if(($userController->isStationAdmin($request->user()) && $station->stationAdmin->user_id == $request->user()->id) || $userController->isSuperAdmin($request->user())) {
            StationService::where("service_id",$service_id)->where("station_id",$station_id)->delete();
            return response()->json(["Service removed from station successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function addServiceToStation(Request $request, UserController $userController, $service_id, $station_id) {
        $station = Station::where('id',$station_id)->with("stationAdmin")->get()->first();
        if(!$station)
            return response()->json(["station not found"],404);
        if(($userController->isStationAdmin($request->user()) && $station->stationAdmin->user_id == $request->user()->id) || $userController->isSuperAdmin($request->user())) {
            $service = Service::where('id',$service_id)->get()->first();
            if(!$service)
                return response()->json(["service not found"],404);
            if(StationService::where("service_id",$service_id)->where("station_id",$station_id)->get()->first())
                return response()->json(["Service already affected to station"],200);

             $stationService = StationService::create([
                 "service_id" =>$service_id,
                 "station_id" =>$station_id
            ]);
             $stationService->service = $service;
            return response()->json(["stationService"=>$stationService],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
}
