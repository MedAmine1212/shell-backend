<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function findAvailableServicesToAdd(Request $request, UserController $userController, $station_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $services = DB::table('services')
                ->whereNotIn('id', function($query) use ($station_id) {
                    $query->select('service_id')
                        ->from('station_services')
                        ->where('station_id', '=', $station_id);
                })
                ->get();

            return response()->json(["services" => $services],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function findAllServices(Request $request, UserController $userController){
        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["services" => Service::all()],200);
    } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAllByStationId(Request $request, UserController $userController, $station_id){
            $stationServices = StationService::where("station_id", $station_id)->with("service")->get();
            if(sizeof($stationServices)>0) {
                $services = [];
                $i = 0;
                foreach ($stationServices as $ss) {
                    $services[$i++] = $ss->service;
                }
                return response()->json(["services" => $services],200);
            }
            return response()->json(["services" => []],200);

        }

    public function addService(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["service"=>Service::create([
                'label' => $request->get('label'),
                'price' => $request->get('price'),
                'duration' => $request->get('duration'),
            ])]);
        }else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function updateService(Request $request, UserController $userController, $station_id) {
        if($userController->isSuperAdmin($request->user())) {
            $service = Service::where('id',$station_id)->get()->first();
            if(!$service) return response()->json(["Service not found"], 404);
            if($request->has("label"))
                $service->label = $request->get('label');
            if($request->has("price"))
                $service->price = $request->get('price');
            if($request->has("duration"))
                $service->price = $request->get('duration');
            $service->update();

            return response()->json(["service updated successfully"]);
        }else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function deleteService(Request $request, UserController $userController, $station_id) {
        if($userController->isSuperAdmin($request->user())) {
            $service = Service::where('id',$station_id)->get()->first();
            if(!$service) return response()->json(["Service not found"], 404);
            $service->delete();
            return response()->json(["service deleted successfully"]);
        }else {
            return response()->json(["Forbidden"],403);
        }
    }
}
