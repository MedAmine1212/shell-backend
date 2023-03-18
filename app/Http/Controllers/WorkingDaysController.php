<?php

namespace App\Http\Controllers;

use App\Models\WorkingDays;
use Illuminate\Http\Request;

class WorkingDaysController extends Controller
{

    public function makeWorkingDays($workingDays, $work_schedule_id)
    {
        $savedDays = [];
        foreach ($workingDays as $workingDay) {
            $wd = WorkingDays::create([
                'work_schedule_id' =>$work_schedule_id,
                'day' => $workingDay['day'],
                'working' => $workingDay['working'],
                'shiftStart' => $workingDay['shiftStart'],
                'shiftEnd' => $workingDay['shiftEnd'],
                'pauseStart' => $workingDay['pauseStart'],
                'pauseEnd' => $workingDay['pauseEnd'],
            ]);

            array_push($savedDays, $wd);
        }
        return $savedDays;
    }

    public function updateWorkingDay($working_day_id, UserController $userController, Request $request)
    {
        if($userController->isSuperAdmin($request->user())) {
            $workingDay = WorkingDays::where("id",$working_day_id)->get()->first();
            if(!$workingDay)
                return response()->json(["Working day not found"], 404);

            if($request->has("working"))
                $workingDay->working = $request->get('working');
            if($request->has("shiftStart"))
                $workingDay->shiftStart = $request->get('shiftStart');
            if($request->has("shiftEnd"))
                $workingDay->shiftEnd = $request->get('shiftEnd');
            if($request->has("pauseStart"))
                $workingDay->pauseStart = $request->get('pauseStart');
            if($request->has("pauseEnd"))
                $workingDay->pauseEnd = $request->get('pauseEnd');
            $workingDay->update();
            return response()->json(["workingDay"=>$workingDay], 200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }
}
