<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\DonationCenter;
use App\Traits\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    use ResponseApi;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        DB::enableQueryLog();

        $user = \request()->user();
        $user->load('appointments','appointments.center','appointments.center.location');
        return $this->successResponse([
            'appointments' => AppointmentResource::collection($user->appointments),
//            'query' => DB::getQueryLog()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = \request()->user();
        $validator = Validator::make($request->all(),[
            'center_id' => 'required|integer|exists:donation_centers,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::tomorrow())) {
                        return $fail('The appointment date must be at least tomorrow.');
                    }
                    return true;
                },
            ],
        ]);

        if ($validator->fails()){
            return $this->validationErrors($validator->errors());
        }

        $date = Carbon::parse($request->input('date'));
        $center = DonationCenter::where('id', $request->input('center_id'))->first();
        $appointment = Appointment::where('center_id',$center->id)->latest('time')->first();
        if ($appointment){
            $appointmentTime = Carbon::parse($appointment->time);

            $nextAvailableTime = $appointmentTime->addMinutes(20);
            $fourPm = Carbon::createFromTime(16, 0, 0);
            if ($nextAvailableTime->greaterThanOrEqualTo($fourPm)) {
                return response()->json(['error' => 'No available appointments after 4 PM'], 400);
            }else{
                $newAppointment = Appointment::create([
                   'user_id' => $user->id,
                    'center_id' => $center->id,
                    'status' => 'scheduled',
                    'date' => $request->input('date'),
                    'time' => $nextAvailableTime->format('H:i:s')
                ]);
                return $this->successResponse(['appointment' => $newAppointment],"appointment added at ".$request->input('date')
                    .$nextAvailableTime);
            }
        }else {
            $time = Carbon::createFromTime(8, 0, 0)->format('H:i:s');
            $newAppointment = Appointment::create([
                'user_id' => $user->id,
                'center_id' => $center->id,
                'status' => 'scheduled',
                'date' => $request->input('date'),
                'time' => Carbon::createFromTime(8, 0, 0)->format('H:i:s')
            ]);
            return $this->successResponse(['appointment' => $newAppointment],"appointment added at ".$request->input('date').$time);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
