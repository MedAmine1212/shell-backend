<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Station;
use App\Models\StationAdmin;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function findAllClients(UserController $userController, Request $request) {
            if($userController->isSuperAdmin($request->user())) {
            $clients = Client::all()->with("station");
            return response()->json(['Clients' => $clients], 200);
        } else{
            return response()->json(['error' => 'Unauthorised'], 403);
        }
    }
}
