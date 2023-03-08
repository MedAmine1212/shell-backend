<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationService;
use App\Models\Service;
use Illuminate\Http\Request;

class ConsultationServiceController extends Controller
{
    public function updateStatus(Request $request, UserController $userController, $consultation_id, $service_id) {
        if(!$userController->isClient($request->user())) {
            $consultationService = ConsultationService::where("service_id",$service_id)->where("consultation_id",$consultation_id)->get()->first();
            if(!$consultationService)
                return response()->json(["consultation_service not found"],404);
            ConsultationService::where("service_id",$service_id)->where("consultation_id",$consultation_id)->update(["status"=>$request->get("status")]);
            return response()->json(["status updated"],200);
        } else {
            return response()->json(["Forbidden"],403);
        }
    }

    public function addServiceToConsultation($service_id, $consultation_id) {
        $consultation = Consultation::where('id',$consultation_id)->get()->first();

        if(!$consultation)
            return response()->json(["consultation not found"],404);
           $service = Service::where("id",$service_id)->get()->first();
            if(!$service)
                return response()->json(["service not found"],404);
            if(ConsultationService::where("service_id",$service_id)->where("consultation_id",$consultation_id)->get()->first())
                return response()->json(["service already affected to consultation"],200);

            ConsultationService::create([
                "service_id" =>$service_id,
                "consultation_id" =>$consultation_id
            ]);
            $consultation->duration+=$service->duration;
            $consultation->update();
            return response()->json(["Service affected to consultation successfully"],200);
    }

    public function removeServiceFromConsultation($service_id, $consultation_id) {
        $consultationService = ConsultationService::where("service_id",$service_id)->where("consultation_id",$consultation_id)->get()->first();
        if(!$consultationService)
            return response()->json(["service not affected to consultation"],404);
        ConsultationService::where("service_id",$service_id)->where("consultation_id",$consultation_id)->delete();
        $consultation = Consultation::where("id",$consultation_id)->get()->first();
        $service = Service::where('id',$service_id)->get()->first();
        $consultation->duration-=$service->duration;
        $consultation->update();
            return response()->json(["Service removed from station successfully"],200);
    }
}
