<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\StationAdmin;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function userType($user) {

        $client = Client::where("user_id", $user->id)->get()->first();
        if($client != null) {
            return "client";
        } else {
            $employee = Employee::where("user_id", $user->id)->get()->first();
            if($employee != null) {
               return "employee";
            } else {
                $stationAdmin = StationAdmin::where("user_id", $user->id)->get()->first();
                if($stationAdmin != null) {
                    return "stationAdmin";
                } else {
                    $superAdmin = SuperAdmin::where("user_id", $user->id)->get()->first();
                    if($superAdmin != null) {
                        return "superAdmin";
                    } else {
                        return "User not found";
                    }
                }
            }
        }

    }
    public function getUserType($barCode)
    {
        $user = User::where("barCode", $barCode)->get()->first();
        if(!$user)
            return response()->json(['userType' => "User not found"], 404);
        $userType = $this->userType($user);

        return response()->json(['userType' => $userType], 200);
    }


    public function login(Request $request)
    {
        $u = User::where('barCode', $request->get("barCode"))->get()->first();
        if($this->isClient($u) || $this->isEmployee($u)) {
            $password = "1234";
        } else {
            $password = $request->get("password");
        }
        $credentials = [
            'barCode' => $request->get('barCode'),
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MyAppToken')->accessToken;

            return response()->json(['access_token' => $token]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function addUser(Request $request)
    {


        if(User::where("phone", $request->get("phone"))->get()->first())
            return "phone in use";
        $password = $request->get("password");
        if($password == null) {
            $password = "1234";
        }
        $barCode = $request->get("barCode");
        if($barCode == null) {
            $barCode = $request->get("phone");
        } else {
            if(User::where("barCode", $request->get("barCode"))->get()->first())
                return "barcode in use";
        }
        return User::create([
            'firstName' => $request->get("firstName"),
            'lastName' => $request->get("lastName"),
            'password' => bcrypt($password),
            'barCode' => $barCode,
            'phone' => $request->get("phone")
        ]);
    }





    // check authenticated user
    public function isClient($user) {
       return $this->userType($user) == "client";
    }

    public function isEmployee($user) {
        return $this->userType($user) == "employee";
    }

    public function isStationAdmin($user) {
        return $this->userType($user) == "stationAdmin";
    }

    public function isSuperAdmin($user) {
        return $this->userType($user) == "superAdmin";
    }
}
