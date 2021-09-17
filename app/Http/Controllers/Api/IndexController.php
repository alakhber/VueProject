<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\WorkingHours;
use Exception;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public  function getWorkingHours($date = null)
    {
        $currentDate = is_null($date) ? date('Y-m-d') : $date;
        $returnArray = [];
        $hours = WorkingHours::all();
        foreach ($hours as $key => $hour) {
            $control = Appointment::where('date', $currentDate)
                ->where('workinghour_id', $hour->id)->count();
            $hour['isActive'] = $control == 0 ? true : false;
            $returnArray[] = $hour;
        }
        return  response()->json($returnArray);
    }

    public function addAppointment(Request $request)
    {
        $all = $request->except('_token');
        $returnArray = [];
        $control = Appointment::where('date', $all['date'])->where('workinghour_id', $all['workinghour_id'])->count();
        if($control != 0){
            return  response()->json(['operation'=>false,'message' => 'Rezerv Olunub !']);
        }
        try{
            Appointment::create($all);
            return  response()->json(['operation'=>true,'message' => 'Rezerv Testiqlendi !']);
        }catch(Exception $e){
            return  response()->json(['operation'=>false,'message' => $e->getMessage()]);
        }
    }
}
