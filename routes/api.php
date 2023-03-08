<?php

use App\Http\Controllers\BorneController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\StationServiceController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StationAdminController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/*************************************** Authentication routes ************************************************** */

//auth not required



Route::get('borne/heartbeat/{borne_id}', [BorneController::class, 'heartbeat']);

Route::post('addClient', [ClientController::class, 'addClient']); //done

Route::get('/getUserType/{barCode}',[UserController::class, 'getUserType']); //done

Route::post('/authenticate',[UserController::class, 'login']); //done


Route::get('/product/getImageByProductId/{product_id}', [ProductController::class, 'getImageByProductId']); //done

// Auth required
Route::middleware('auth:api')->group(function () {

    Route::get('/getCurrentUser', function (Request $request) {return $request->user();}); //done


    /*************************************** Employee routes ************************************************** */

    Route::prefix('employee/')->group(function () {
        Route::get('findAllByStationId/{station_id}', [EmployeeController::class, 'findAllByStationId']); //done
        Route::get('assignToStation/{employee_id}/{station_id}', [EmployeeController::class, 'assignEmployeeToStation']); //done
        Route::post('add/{station_id}', [EmployeeController::class, 'addEmployee']); //done

    });

    /*************************************** Station admin routes ************************************************** */

    Route::prefix('stationAdmin/')->group(function () {
        Route::post('add', [StationAdminController::class, 'addStationAdmin']); //done
        Route::get('findAll', [StationAdminController::class, 'findAll']); //done
        Route::get('assignStationAdmin/{station_admin_id}/{station_id}', [StationAdminController::class, 'assignAdminToStation']); //done
    });

    /*************************************** Super admin routes ************************************************** */

    Route::prefix('superAdmin/')->group(function () {
        Route::get('findAllClients', [SuperAdminController::class, 'findAllClients']); //done

    });

    /*************************************** Clients routes ************************************************** */

    Route::prefix('client/')->group(function () {
        Route::get('findAllUnvalidated', [ClientController::class, 'findAllUnvalidated']); //done
        Route::get('findAllUnvalidatedByStationId/{station_id}', [ClientController::class, 'findAllUnvalidatedByStationId']); //done
        Route::get('validateClient/{id}/{barCode}', [ClientController::class, 'validateClient']); //done

    });

    /*************************************** Vehicles routes ************************************************** */


    Route::prefix('vehicle/')->group(function () {
        Route::get('findAllByClientId/{client_id}', [VehicleController::class, 'findAllByClientId']); //done
        Route::post('add/{client_id}', [VehicleController::class, 'add']); //done
        Route::post('update/{vehicle_id}', [VehicleController::class, 'update']); //done
        Route::delete('delete/{vehicle_id}', [VehicleController::class, 'delete']); //done
    });

    /*************************************** Stations routes ************************************************** */

    Route::prefix('station/')->group(function () {
        Route::get('findAll', [StationController::class, 'getAllStations']); //done
        Route::get('findById/{station_id}', [StationController::class, 'findById']); //done
        Route::get('getOneByAdminId/{station_admin_id}', [StationController::class, 'getStationByAdminId']); //done
        Route::post('add', [StationController::class, 'addStation']); //done
        Route::put('update/{station_id}', [StationController::class, 'update']); //done
        Route::delete('delete/{station_id}', [StationController::class, 'delete']); // done
    });

    /*************************************** Bornes routes ************************************************** */

    Route::prefix('borne/')->group(function () {
        Route::get('findAllByStationId/{station_id}', [BorneController::class, 'findAllByStationId']); //done
        Route::post('add/{station_id}', [BorneController::class, 'addBorne']); //done
        Route::post('changeInterval/{borne_id}', [BorneController::class, 'changeInterval']); //done
        Route::delete('delete/{borne_id}', [BorneController::class, 'deleteBorne']); //done
    });

    /*************************************** Products routes ************************************************** */

    Route::prefix('product/')->group(function () {
        Route::get('findAll', [ProductController::class, 'getAllProducts']); //done
        Route::get('findAllByStationId/{station_id}', [ProductController::class, 'findAllByStationId']); //done

        //add new product or add quantity
        Route::post('add', [ProductController::class, 'addProduct']); //done
        Route::post('update/{product_id}', [ProductController::class, 'updateProduct']); //done
        Route::delete('delete/{product_id}', [ProductController::class, 'deleteProduct']); //done
    });

    /*************************************** Services routes ************************************************** */

    Route::prefix('service/')->group(function () {
        Route::get('findAll', [ServiceController::class, 'findAllServices']); //done
        Route::get('findAllByStationId/{station_id}', [ServiceController::class, 'findAllByStationId']); //done
        Route::post('add', [ServiceController::class, 'addService']); //done
        Route::put('update/{service_id}', [ServiceController::class, 'updateService']); //done
        Route::delete('delete/{service_id}', [ServiceController::class, 'deleteService']); //done
    });

    /*************************************** Consultations routes ************************************************** */

    Route::prefix('consultation/')->group(function () {
        Route::get('findAllByVehicleId/{vehicle_id}', [ConsultationController::class, 'findAllByVehicleId']); //done
        Route::get('findAllByEmployeeId/{employee_id}', [ConsultationController::class, 'findAllByEmployeeId']); //done
        Route::get('getAvailableTimeSlots/{station_id}/{consultation_id}', [ConsultationController::class, 'getAvailableTimeSlots']); //done
        Route::post('create', [ConsultationController::class, 'addConsultation']); //done
        Route::post('confirmConsultationDate/{consultation_id}/{station_id}', [ConsultationController::class, 'confirmConsultationDate']); //done
    });


    /*************************************** Station_service routes ************************************************** */

    Route::prefix('station_service/')->group(function () {
        Route::get('addServiceToStation/{service_id}/{station_id}', [StationServiceController::class, 'addServiceToStation']); //done
        Route::get('removeServiceFromStation/{service_id}/{station_id}', [StationServiceController::class, 'removeServiceFromStation']); //done

    });

    /*************************************** Consultation_service routes ************************************************** */

    Route::prefix('consultation_service/')->group(function () {
        Route::post('updateStatus/{consultation_id}/{service_id}', [ConsultationServiceController::class, 'updateStatus']); //done
        Route::get('addServiceToConsultation/{service_id}/{consultation_id}', [ConsultationServiceController::class, 'addServiceToConsultation']); // done
        Route::get('removeServiceFromConsultation/{service_id}/{consultation_id}', [ConsultationServiceController::class, 'removeServiceFromConsultation']); //done
    });

    /*************************************** Product_station routes ************************************************** */

    Route::prefix('product_station/')->group(function () {

        //if product already assigned to station it adds the stock. (can send negative stock value to decrease stock !)
        Route::post('addProductToStation/{product_id}/{station_id}', [ProductStationController::class, 'addProductToStation']); //done
        Route::get('removeProductFromStation/{product_id}/{station_id}/{restoreStock}', [ProductStationController::class, 'removeProductFromStation']); //done
    });

    /*************************************** Work schedule routes ************************************************** */

    Route::prefix('workSchedule/')->group(function () {
        //pass station_id to assign workSchedule to station directly
        Route::post('create', [WorkScheduleController::class, 'createWorkSchedule']); //done
        Route::get('assignToStation/{station_id}/{word_schedule_id}', [WorkScheduleController::class, 'assignToStation']); //done
        Route::put('update/{work_schedule_id}', [WorkScheduleController::class, 'updateWorkSchedule']); //done
        Route::delete('delete/{work_schedule_id}', [WorkScheduleController::class, 'deleteWorkSchedule']); //done
    });

//remove token
Route::post('/logout',[UserController::class, 'logout']); //done
});
