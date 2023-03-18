<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    public function unassignFromStation(Request $request, UserController $userController, $employee_id, $station_id) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            $emp = Employee::where('id',$employee_id)->get()->first();
            if(!$emp)
                return response()->json(["Employee not found"],404);
            $emp->station_id = null;
            $emp->update();
            return response()->json(["Unassigned successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function getAllUnassigned(Request $request, UserController $userController) {

        if($userController->isSuperAdmin($request->user()) || $userController->isStationAdmin($request->user())) {
            return response()->json(["employees"=>Employee::where("station_id",null)->with("user")->get()],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function addEmployee(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
        $user = $userController->addUser($request, true);
             if($user == "email in use") {
                return response()->json(["Email already in use"],401);
            } else if($user == "phone in use") {
                return response()->json(["Phone number already in use"],200);
        } else if ($user == "barcode in use") {
                return response()->json(["Barcode already assigned"],200);
        } else {
            $employee = Employee::create([
                'user_id' => $user->id
            ]);
            $employee->user = $user;
            return response()->json(["employee"=>$employee], 200);
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

    public function findAll(Request $request, UserController $userController) {
        if($userController->isSuperAdmin($request->user())) {
            return response()->json(["employees"=>Employee::with("user")->with("station")->with('consultations')->get()],200);
        } else {

            return response()->json(["Forbidden"],403);
        }
    }
}
