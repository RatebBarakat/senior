<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\DonationCenter;
use App\Models\Social;
use App\Models\User;
use App\Traits\ResponseApi;
use Illuminate\Http\JsonResponse;
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
    public function store(Request $request): JsonResponse
    {
        $user = \request()->user();

        if ((string) $errorMessage = $this->canScheduleAppointment($user)) {
            return $this->responseError($errorMessage);
        }

        $validator = Validator::make($request->all(), [
            'center_id' => 'required|integer|exists:donation_centers,id',
            'date' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::tomorrow())) {
                        $fail('The appointment date must be at least tomorrow.');
                    }
                },
            ],
        ],[
            'center_id.exists' => 'The donation center does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $date = Carbon::parse($request->input('date'));
        $center = DonationCenter::findOrFail($request->input('center_id'));
        $appointment = Appointment::where('center_id', $center->id)
            ->whereDate('date',$date)->latest('time')->first();

//        if ($appointment && Carbon::parse($appointment->time)->addMinutes(20)
//                ->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
//            return $this->validationErrors(['error' => 'No available appointments after 4 PM']);
//        }

        $nextAvailableTime = $this->getNextAvailableTime($appointment);

        if ($nextAvailableTime == null)
            return $this->responseError('you cannot take an appointment at this day');

        $newAppointment = $this->createNewAppointment($user, $center, $date, $nextAvailableTime);

        return $this->successResponse(['appointment' => $newAppointment],
            "Appointment added on " . $date->format('l, F jS, Y') . " at " . $nextAvailableTime->format('g:i A'));
    }

    private function createNewAppointment($user, DonationCenter $center, Carbon $date, Carbon $time)
    {
        return Appointment::create([
            'user_id' => $user->id,
            'center_id' => $center->id,
            'status' => 'scheduled',
            'date' => $date->format('Y-m-d'),
            'time' => $time->format('H:i:s')
        ]);
    }

    private function getNextAvailableTime(?Appointment $appointment)
    {
        if (!$appointment) {
            return Carbon::createFromTime(8, 0, 0); //at 8 PM
        }

        $appointmentTime = Carbon::parse($appointment->time);
        $nextAvailableTime = $appointmentTime->addMinutes(20);

        if ($nextAvailableTime->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
            return null;
        }

        return $nextAvailableTime;
    }

    private function canScheduleAppointment(Social|User $user): ?string
    {
        $now = Carbon::now();
        $twoMonthsFromNow = $now->copy()->addMonths(2);

        if ($user->appointments()
            ->scheduled()
            ->whereBetween('date', [$now, $twoMonthsFromNow])
            ->exists()) {
            return 'Sorry, you cannot make another appointment at this time. You already have an appointment scheduled within the next two months. This is because blood donation is generally recommended once every two months to ensure your body has enough time to recover fully.';
        }

        $lastAppointment = $user->appointments()->latest('date')->first();

        if (!$lastAppointment) {
            return null;
        }

        $lastAppointmentDate = Carbon::parse($lastAppointment->date);
        $eligibleDate = $lastAppointmentDate->addMonths(2);

        if ($eligibleDate->isFuture()) {
            return "Your last appointment was on {$lastAppointmentDate->format('l, F jS, Y')}, so you cannot make another appointment before {$eligibleDate->format('l, F jS, Y')}.";
        }

        return null;
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Appointment::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = \request()->user();
        $appointment = Appointment::scheduled()
            ->where(function ($q) use ($user,$id) {
                $q->where('user_id',$user->id)
                    ->where('id',$id);
            })->first();

        if (!$appointment){
            return $this->responseError('the appointment doesnt exists');
        }

        $validator = Validator::make($request->all(), [
            'center_id' => 'required|integer|exists:donation_centers,id',
            'date' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::tomorrow())) {
                        $fail('The appointment date must be at least tomorrow.');
                    }
                },
            ],
        ],[
            'center_id.exists' => 'The donation center does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $date = Carbon::parse($request->input('date'));
        $center = DonationCenter::findOrFail($request->input('center_id'));
        $appointmentAtDate = Appointment::where('center_id', $center->id)
            ->whereDate('date',$date)->latest('time')->first();

//        if ($appointmentAtDate && Carbon::parse($appointmentAtDate->time)->addMinutes(20)
//                ->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
//            return $this->validationErrors(['error' => 'No available appointments after 4 PM']);
//        }
        $nextAvailableTime = $this->getNextAvailableTime($appointmentAtDate);
        if ($nextAvailableTime == null)
            return $this->responseError('you cannot take an appointment at this day');

        $updatedAppointment = $appointment->update([
            'center_id' => $request->input('center_id'),
            'date' => $request->input('date'),
            'time' => $nextAvailableTime
        ]);

        return $this->successResponse(['appointment' => $appointment],
            "Appointment added on " . $date->format('l, F jS, Y') . " at " . $nextAvailableTime->format('g:i A'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $appointment = Appointment::scheduled()->find($id);
        if (!$appointment){
            return $this->responseError('appointment not found');
        }
        $appointment->delete();
        return $this->successResponse([],'appointment deleted successfully');
    }
}
