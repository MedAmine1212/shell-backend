<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Station;
use App\Models\StationAdmin;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{


    public function addClient(Request $request, UserController $userController) {

        $user = $userController->addUser($request, true);
        if($user == "email in use") {
            return response()->json(["Email already in use"],401);
        } else if($user == "phone in use") {
            return response()->json(["Phone number already in use"],401);
        } else if ($user == "barcode in use") {
           return response()->json(["Barcode already assigned"],401);
        } else {
            $registeredAt = null;
            if($request->has("registeredAt"))
                $registeredAt = $request->get("registeredAt");
            $client = Client::create([
                'user_id' => $user->id,
                'registeredAt' => $registeredAt
            ]);
            $client->user = $user;
            return response()->json(["client" => $client], 200);
        }
    }

    public function validateClient($id, $barCode, UserController $userController, Request $request) {
        $client = Client::where("id", $id)->with('user')->get()->first();
        $authorized = false;
        if($client) {
        if($userController->isStationAdmin($request->user())) {
            $stationAdmin = StationAdmin::where("user_id",$request->user()->id)->get()->first();
            $station = Station::where('station_admin_id',$stationAdmin->id)->get()->first();
            if($station->id == $client->registeredAt)
                $authorized = true;
        } else {
            if($userController->isSuperAdmin($request->user()))
                $authorized = true;
        }
        if($authorized) {
            $u = User::where("barCode", $barCode)->get()->first();
            if($u) {
                if( $u->id != $client->user->id)
                    return response()->json(["Barcode already assigned !"], 401);
            }
            $client->user->barCode = $barCode;
            $client->user->update();
            $client->validated = true;
            $client->update();

            //Send SMS to client

            return response()->json(["client validated successfully !"], 200);
        }  else {
            return response()->json(["Forbidden"],403);
        }
        }
        return response()->json(["user not found"],404);
    }

    public function findAllUnvalidated(UserController $userController, Request $request) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["unvalidatedClients" => Client::where('validated', false)->with('user')->get()]);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAllUnvalidatedByStationId(UserController $userController, Request $request, $station_id) {
        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["unvalidatedClients" => Client::where('validated', false)->where('registeredAt',$station_id)->with('user')->get()]);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAllClients(UserController $userController, Request $request) {
        if($userController->isSuperAdmin($request->user())) {
            $clients = Client::with("station")->with("user")->get();
            return response()->json(['clients' => $clients], 200);
        } else{
            return response()->json(['error' => 'Unauthorised'], 403);
        }
    }
}
