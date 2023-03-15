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

    public function  delete(Request $request, UserController $userController, $user_id) {

        if($userController->isSuperAdmin($request->user())) {
            $user = User::where("id",$user_id)->get()->first();
            if(!$user)
                return response()->json(["User not found"],404);
            $user->delete();
            return response()->json(["user deleted successfully"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }
    public function update(Request $request, $user_id) {
        if($this->isSuperAdmin($request->user())) {
            try {
                $u = User::where("id", $user_id)->get()->first();
                $u->firstName = $request->get("firstName");
                $u->lastName = $request->get("lastName");
                $u->email = $request->get("email");
                $u->phone = $request->get("phone");
                $u->update();
                return response()->json(["User updated"], 200);
            } catch (\Exception $ex) {
                return response()->json([$ex->getMessage()], 403);
            }
        }
    }
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


    public function loginClient(Request $request) {

        $u = User::where("barCode", $request->get("barCode"))->get()->first();
        if($u) {
        $userType = $this->userType($u);
        if($userType == "client" || $userType == "employee") {
            $password = "1234";
            $credentials = [
                'barCode' => $request->get('barCode'),
                'password' => $password,
            ];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('MyAppToken')->accessToken;

                return response()->json(['access_token' => $token, 'user_type'=>$userType]);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        }
        }
        return response()->json(['error' => 'user not found'], 404);
    }
    public function loginDashBoard(Request $request)
    {
        $u = User::where("email", $request->get("email"))->get()->first();
        if($u){
        if($this->isStationAdmin($u) || $this->isSuperAdmin($u)) {
            $userType = $this->userType($u);
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get("password"),
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MyAppToken')->accessToken;

            return response()->json(['access_token' => $token, 'user_type'=>$userType]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        }
        }
        return response()->json(['error' => 'user not found'], 404);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function addUser(Request $request, $sender)
    {

        if(User::where("email", $request->get("email"))->get()->first())
            return "email in use";
        if(User::where("phone", $request->get("phone"))->get()->first())
            return "phone in use";
        $barCode = $request->get("barCode");
        if($barCode == null) {
            $barCode = $request->get("phone");
        } else {
            if(User::where("barCode", $request->get("barCode"))->get()->first())
                return "barcode in use";
        }
        $password = $request->get("password");
        if($password == null) {
            if($sender)
                $password = "1234";
            else return "password required";
        }
        return User::create([
            'firstName' => $request->get("firstName"),
            'lastName' => $request->get("lastName"),
            'password' => bcrypt($password),
            'barCode' => $barCode,
            'email' => $request->get("email"),
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
