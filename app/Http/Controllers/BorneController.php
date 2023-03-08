<?php

namespace App\Http\Controllers;


use App\Events\StationAdminEvent;
use App\Events\SuperAdminEvent;
use App\Jobs\BorneHeartBeatChecker;
use App\Models\Borne;
use App\Models\BorneDisconnectedMessage;
use App\Models\Station;
use Illuminate\Http\Request;

class BorneController extends Controller
{

    public function changeInterval($borne_id, Request $request, UserController $userController) {

        $borne = Borne::where("id",$borne_id)->get()->first();
        if(!$borne)
            return response()->json('Borne not found', 404);
        $station = Station::where("id",$borne->station_id)->with("stationAdmin")->get()->first();

        if ($station->stationAdmin->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {
            $borne->heartBeatInterval = $request->get("heartBeatInterval");
            $borne->update();
            return response()->json('Heartbeat interval updated', 200);
        } else {
            return response()->json(["Forbidden"],403);
        }

    }
    public function heartbeat($borne_id){
        $borne = Borne::where("id",$borne_id)->get()->first();
        if(!$borne)
            return response()->json('Borne not found', 404);
        if(!$borne->status) {
            event(new SuperAdminEvent(new BorneDisconnectedMessage("borneConnected",$borne->station_id,$borne_id)));
            event(new StationAdminEvent(new BorneDisconnectedMessage("borneConnected",$borne->station_id,$borne_id)));
            BorneHeartBeatChecker::dispatch($borne_id,$borne->heartBeatInterval)
                ->delay(now()->addMinutes($borne->heartBeatInterval))
                ->onConnection('database');
        }
        $borne->lastHeartBeat = now()->addMinutes(60)->format('Y-m-d H:i');
        $borne->status = true;
        $borne->update();
        return response()->json('Heartbeat updated', 200);

    }
    public function deleteBorne($borne_id, Request $request, UserController $userController) {
        $borne = Borne::where("id",$borne_id)->get()->first();
        if(!$borne)
            return response()->json('Borne not found', 404);
        $station = Station::where("id",$borne->station_id)->with("stationAdmin")->get()->first();

        if ($station->stationAdmin->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {
            $borne->delete();
            return response()->json(["Borne deleted successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }

    }

    public function findAllByStationId($station_id) {
        return response()->json(["bornes"=>Borne::where("station_id",$station_id)->get()],200);
    }


    public function addBorne(Request $request, UserController $userController, $station_id)
    {

        $station = Station::where("id", $station_id)->with("stationAdmin")->get()->first();
        if (!$station) {
            return response()->json('Station not found', 404);
        }
        if ($station->stationAdmin->user_id == $request->user()->id || $userController->isSuperAdmin($request->user())) {

            $borne = Borne::create([
                'status' => false,
                'heartBeatInterval' => $request->get("heartBeatInterval"),
                'station_id' => $station_id
            ]);
            return response()->json(["borne" => $borne], 200);
        }else{
            return response()->json(["Forbidden"],403);
        }
    }
}
