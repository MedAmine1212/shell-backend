<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Employee;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function findAllByVehicleId($vehicle_id) {
        return response()->json(["consultations" => Consultation::where("vehicle_id", $vehicle_id)->with("employee")->get()],200);
    }

    public function findAllByEmployeeId($employee_id) {
        return response()->json(["consultations" => Consultation::where("employee_id", $employee_id)->with("vehicle")->get()],200);
    }

    public function addConsultation(Request $request) {
        return response()->json(["consultation" => Consultation::create([
            'type' => $request->get('type'),
            'vehicle_id' => $request->get('vehicle_id'),
            'duration' => 0,
        ])],200);
    }

    public function confirmConsultationDate(Request $request, $consultation_id, $station_id) {

        $currentConsultation = Consultation::leftJoin('consultation_services', 'consultations.id', '=', 'consultation_services.consultation_id')
            ->leftJoin('services', 'services.id', '=', 'consultation_services.service_id')
            ->where('consultations.id', $consultation_id)
            ->groupBy('consultations.id')
            ->select('consultations.id', DB::raw('SUM(services.duration) as duration'))->get()->first();
        if(!$currentConsultation)
            return response()->json(["Consultation not found"],404);

        $currentDate = now()->format('Y-m-d');
        $employees = Employee::where('station_id', $station_id)->get();
        foreach ($employees as $employee) {
            $employee->consultations = DB::table('consultations as c')
                ->select('c.id', 'c.dateConsultation',DB::raw('SUM(s.duration) as duration'))
                ->where("c.employee_id", $employee->id)
                ->whereBetween('c.dateConsultation', [$currentDate.' 00:00:00', $currentDate.' 23:59:59'])
                ->leftJoin('consultation_services as cs', 'cs.consultation_id', '=', 'c.id')
                ->leftJoin('services as s', 'cs.service_id', '=', 's.id')
                ->groupBy('c.id', 'c.dateConsultation')
                ->get();
        }

        //get the available employees at the time of the consultation
        $available = [];
        $endConsultationTime = Carbon::parse($request->get("time"))->addMinutes($currentConsultation->duration)->format('H:i');

        foreach ($employees as $emp) {
            if(sizeof($emp->consultations) == 0)
                array_push($available,$emp);
            else {
                foreach ($emp->consultations as $con) {
                    if ((Carbon::parse($request->get("time"))->format('H:i') >= Carbon::parse($con->dateConsultation)->format('H:i') && Carbon::parse($request->get("time"))->format('H:i') < Carbon::parse($con->dateConsultation)->addMinutes($con->duration)->format('H:i')) || (Carbon::parse($endConsultationTime)->format('H:i') > Carbon::parse($con->dateConsultation)->format('H:i') && Carbon::parse($endConsultationTime)->format('H:i') < Carbon::parse($con->dateConsultation)->addMinutes($con->duration)->format('H:i'))) {
                        $index = array_search($emp, $available);
                        if ($index !== false) {
                            unset($available[$index]);
                        }
                    } else {
                        if (!in_array($emp, $available)) {
                            array_push($available, $emp);
                        }
                    }
            }
            }
        }

            //get the employee with the least consultations
        $employee = $available[0];

        for ($i =1;$i<sizeof($available);$i++) {
            if(sizeof($available[$i]->consultations)<sizeof($employee->consultations)) {
                $employee = $available[$i];
            }
        }

        $currentConsultation->dateConsultation = now()->format('Y-m-d')." ".Carbon::parse($request->get("time"))->format('H:i');
        $currentConsultation->employee_id = $employee->id;
        $currentConsultation->update();
        return response()->json(["Date confirmed"],200);
    }

    //get available time slots considering : WorkSchedule, Employees shifts, reserved Times.
    public function getAvailableTimeSlots($station_id, $consultation_id)
    {
        $station = Station::where('id',$station_id)->with("workSchedule")->get()->first();
        if(!$station)
            return response()->json(["Station not found"],404);
        $workSchedule = $station->workSchedule;
        $occupiedTimes = $this->getOccupiedTimes($station_id, $consultation_id, $workSchedule);

        $currentConsultation = Consultation::leftJoin('consultation_services', 'consultations.id', '=', 'consultation_services.consultation_id')
            ->leftJoin('services', 'services.id', '=', 'consultation_services.service_id')
            ->where('consultations.id', $consultation_id)
            ->groupBy('consultations.id')
            ->select('consultations.id', DB::raw('SUM(services.duration) as duration'))->get()->first();
        if(!$currentConsultation)
            return response()->json(["Consultation not found"],404);

        $availableTimes = $this->getAvailableTimes($workSchedule, $occupiedTimes, $currentConsultation);
        return response()->json(["available"=>$availableTimes, 'reserved' => $occupiedTimes, 'duration' => $currentConsultation->duration]);
    }


    public function getAvailableTimes($workSchedule, $occupiedTimes, $currentConsultation) {
        $i = 0;
        $availableTimes = [];
        $time = Carbon::parse($workSchedule->shiftStart)->format('H:i');

        while ($time < Carbon::parse($workSchedule->shiftEnd)->format('H:i')) {
            if(!(Carbon::parse($time)->format('H:i') >= Carbon::parse($workSchedule->pauseStart)->format('H:i') && Carbon::parse($time)->format('H:i') < Carbon::parse($workSchedule->pauseEnd)->format('H:i')) && (Carbon::parse($time)->format('H:i') >= Carbon::parse(now())->addMinutes(60)->format('H:i'))) {
                $availableTimes[$i++] =$time;
            }
            $time = Carbon::parse($time)->addMinutes($workSchedule->minimumConsultationTime)->format('H:i');
        }
            for ($i=0;$i<sizeof($availableTimes);$i++) {
                $busy = 0;

                for ($j = 0; $j < sizeof($occupiedTimes); $j++) {
                    for ($k = 0; $k < sizeof($occupiedTimes[$j]); $k++) {
                        $endConsultationTime = Carbon::parse($time)->addMinutes($currentConsultation->duration)->format('H:i');
                        if((Carbon::parse($time)->format('H:i') >= Carbon::parse($occupiedTimes[$j][$k][0])->format('H:i') && Carbon::parse($time)->format('H:i') < Carbon::parse($occupiedTimes[$j][$k][1])->format('H:i')) || (Carbon::parse($endConsultationTime)->format('H:i') > Carbon::parse($occupiedTimes[$j][$k][0])->format('H:i') && Carbon::parse($endConsultationTime)->format('H:i') < Carbon::parse($occupiedTimes[$j][$k][1])->format('H:i'))){
                            $busy++;
                            break;
                        }
                    }
                }
                if($busy == sizeof($occupiedTimes)) {
                    unset($availableTimes[$i]);
                }
            }
        return $availableTimes;
    }

    public function getOccupiedTimes($station_id, $consultation_id, $workSchedule) {
        $currentDate = now()->format('Y-m-d');
        $employees = Employee::where("station_id", $station_id)->with('consultations', function ($query) use ($currentDate) {
            $query->whereRaw("dateConsultation BETWEEN '".$currentDate." 00:00:00' AND '".$currentDate." 23:59:59'");
        })->get();

        $occupiedTimes = [];
        $i = 0;
        foreach ($employees as $employee) {
            $j=0;
            $empOccupied = [];
            foreach ($employee->consultations as $consultation) {
                if($consultation->id != $consultation_id) {
                    $consultation->duration = 80;
                    $consultationStart = Carbon::parse($consultation->dateConsultation);
                    $consultationEnd = $consultationStart->copy()->addMinutes($consultation->duration);
                    $empOccupied[$j++] = [Carbon::parse($consultationStart)->format('H:i'), Carbon::parse($consultationEnd)->format('H:i')];
                }
            }
            $occupiedTimes[$i++] = $empOccupied;
        }
        return $occupiedTimes;
    }
}
