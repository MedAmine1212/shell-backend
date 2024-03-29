<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VehicleController extends Controller
{
    function findAllByClientId($client_id, Request $request, UserController $userController) {
        $client = Client::where('id',$client_id)->with('user')->with('vehicles')->get()->first();
        if(!$client)
            return response()->json(["Client not found"],404);
        if($client->user_id == $request->user()->id || $userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["client" => $client]);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    function add($client_id, Request $request, UserController $userController) {

        $client = Client::where('id',$client_id)->get()->first();
        if(!$client)
            return response()->json(["Client not found"],404);

        if($client->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {
            $lastOilChange = null;
            if($request->has('lastOilChange') && $request->get('lastOilChange') != null) {
                $lastOilChange = Carbon::parse($request->get('lastOilChange'))->format('Y-m-d H:i');
            }
            if(Vehicle::where("matricule", $request->get('matricule'))->get()->first()){
                return response()->json(["Matricule already used"],403);
            }
            return response()->json(["vehicle"=>Vehicle::create([
                'matricule' => $request->get('matricule'),
                'brand' => $request->get('brand'),
                'model' => $request->get('model'),
                'year' => $request->get('year'),
                'fuelType' => $request->get('fuelType'),
                'mileage' => $request->get('mileage'),
                'lastOilChange' => $lastOilChange,
                'client_id' => $client_id
            ])]);
    } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function update($vehicle_id, Request $request, UserController $userController)
    {
        $vehicle = Vehicle::where('id',$vehicle_id)->with("client")->get()->first();

        if(!$vehicle)
            return response()->json(["Vehicle not found"],404);

        if($vehicle->matricule != $request->get("matricule")) {
            if(Vehicle::where("matricule", $request->get('matricule'))->get()->first()){
                return response()->json(["Matricule already used"],403);
            }
        }
        if ($vehicle->client->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {
            if($request->has("matricule"))
                $vehicle->matricule = $request->get('matricule');
            if($request->has("brand"))
                $vehicle->brand = $request->get('brand');
            if($request->has("model"))
                $vehicle->model = $request->get('model');
            if($request->has("year"))
                $vehicle->year = $request->get('year');
            if($request->has("type"))
                $vehicle->type = $request->get('type');
            if($request->has("fuelType"))
                $vehicle->fuelType = $request->get('fuelType');
            if($request->has("mileage"))
                $vehicle->mileage = $request->get('mileage');
            if($request->has("lastOilChange")) {
                $vehicle->lastOilChange = Carbon::parse($request->get('lastOilChange'))->format('Y-m-d H:i');
                }
            $vehicle->update();
            return response()->json(["vehicle"=>$vehicle],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function delete($vehicle_id, Request $request, UserController $userController) {
        $vehicle = Vehicle::where('id',$vehicle_id)->with("client")->get()->first();
        if(!$vehicle)
            return response()->json(["Vehicle not found"],404);

        if ($vehicle->client->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {
            $vehicle->delete();
            return response()->json(["Vehicle removed successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

}
