<?php

namespace App\Http\Controllers;


use App\Models\Station;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{

    public function updateWorkSchedule(Request $request, UserController $userController, $wok_schedule_id) {
        if ($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::where('id',$wok_schedule_id)->get()->first();
            if(!$workSchedule)
                return response()->json(["Work schedule not found"], 404);

            if($request->has("shiftStart"))
                $workSchedule->shiftStart = $request->get('shiftStart');
            if($request->has("shiftEnd"))
                $workSchedule->shiftEnd = $request->get('shiftEnd');
            if($request->has("pauseStart"))
                $workSchedule->pauseStart = $request->get('pauseStart');
            if($request->has("pauseEnd"))
                $workSchedule->pauseEnd = $request->get('pauseEnd');
            if($request->has("minimumConsultationTime"))
                $workSchedule->minimumConsultationTime = $request->get('minimumConsultationTime');
            $workSchedule->update();
            return response()->json(["Schedule updated successfully"], 200);
        } else {
            return response()->json(["Forbidden"], 403);
        }
    }
    public function assignToStation(Request $request, UserController $userController, $station_id, $work_schedule_id)
    {
        if ($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::where('id',$work_schedule_id)->get()->first();
            if(!$workSchedule)
                return response()->json(["Work schedule not found"], 404);

            $station = Station::where('id',$station_id)->get()->first();
            if(!$station)
                return response()->json(["station not found"], 404);

            $station->work_schedule_id = $workSchedule->id;
            $station->update();
            return response()->json(["Work schedule assigned"],200);
        } else {
            return response()->json(["Forbidden"], 403);
        }
    }
    public function createWorkSchedule(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::create([
                'shiftStart' => $request->get('shiftStart'),
                'shiftEnd' => $request->get('shiftEnd'),
                'pauseStart' => $request->get('pauseStart'),
                'pauseEnd' => $request->get('pauseEnd'),
                'minimumConsultationTime' => $request->get('minimumConsultationTime')]);
            if($request->has("station_id")) {
                Station::where('id',$request->get("station_id"))->update(["work_schedule_id" => $workSchedule->id]);
            }
            return response()->json(["workScheduke"=>$workSchedule],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function deleteWorkSchedule($work_schedule_id, Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::where('id',$work_schedule_id)->get()->first();
            if(!$workSchedule)
                return response()->json(["Work schedule not found"], 404);
            $workSchedule->delete();
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
