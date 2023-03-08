<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    public function addEmployee(Request $request, $station_id, UserController $userController) {
        if($userController->isStationAdmin($request->user()) || $userController->isSuperAdmin($request->user())) {
        $station = Station::where('id',$station_id)->get()->first();
        if(!$station)
            return response()->json("Station not found !", 404);
        $user = $userController->addUser($request);
            if($user == "phone in use") {
                return response()->json(["Phone number already in use"],200);
        } else if ($user == "barcode in use") {
                return response()->json(["Barcode already assigned"],200);
        } else {
            $employee = Employee::create([
                'user_id' => $user->id,
                'station_id' => $station_id
            ]);
            return response()->json(["Employee added successfully !"=>$employee], 200);
        }
        } else {
            return response()->json(["Forbidden"],403);
        }
    }


    public function assignEmployeeToStation($employee_id, $station_id, Request  $request, UserController $userController) {

        if($userController->isSuperAdmin($request->user())) {
           $emp = Employee::where('id',$employee_id)->get()->first();
           if(!$emp)
               return response()->json(["Employee not found"],404);
           $emp->station_id = $station_id;
           $emp->update();
            return response()->json("Employee assigned successfully !", 200);
        } else{
            return response()->json(["Forbidden"],403);
        }
    }

    public function findAllByStationId($station_id, Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["employees"=>Employee::where("station_id",$station_id)->with("user")->get()],200);
        } else{

            return response()->json(["Forbidden"],403);
        }
    }
}
