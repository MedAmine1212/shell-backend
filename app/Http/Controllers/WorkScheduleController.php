<?php

namespace App\Http\Controllers;


use App\Models\Station;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{

    public function findAll(Request $request, UserController $userController) {
        if ($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["workSchedules"=>WorkSchedule::with("workingDays")->get()], 200);
        }
    }
    public function updateWorkSchedule(Request $request, UserController $userController, $wok_schedule_id) {
        if ($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::where('id',$wok_schedule_id)->get()->first();
            if(!$workSchedule)
                return response()->json(["Work schedule not found"], 404);
            if($request->has("minimumConsultationTime"))
                $workSchedule->minimumConsultationTime = $request->get('minimumConsultationTime');
            $workSchedule->update();
            return response()->json(["workSchedule"=>$workSchedule], 200);
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
    public function createWorkSchedule(Request $request, UserController $userController, WorkingDaysController $workingDaysController) {
        if($userController->isSuperAdmin($request->user())) {
            $workSchedule = WorkSchedule::create([
                'minimumConsultationTime' => $request->get('minimumConsultationTime')]);
            if($request->has("station_id")) {
                Station::where('id',$request->get("station_id"))->update(["work_schedule_id" => $workSchedule->id]);
            }
            $workSchedule->working_days = $workingDaysController->makeWorkingDays($request->get("days"), $workSchedule->id);
            return response()->json(["workSchedule"=>$workSchedule],200);
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
            return response()->json(["deleted successfully"], 200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
