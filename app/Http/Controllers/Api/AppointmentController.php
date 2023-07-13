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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

        $user->load('appointments', 'appointments.center', 'appointments.center.location');
        return AppointmentResource::collection($user->appointments);
        //            'query' => DB::getQueryLog()
    }

    public function downloadPdf(int $id)
    {
        $appointment = Appointment::findOrFail($id);
        $filename = storage_path('app/public/pdf/' . $appointment->pdf_file);

        if (file_exists($filename)) {
            $fileContents = file_get_contents($filename);

            return response()->make($fileContents, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $appointment->pdf_file . '"'
            ]);
        } else {
            return response()->json(['message' => 'File not found'], 404);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = \request()->user();

        if ((string) $errorMessage = $this->canScheduleAppointment($user)) {
            return $this->validationErrors(['general' => [$errorMessage]]);
        }

        $validator = Validator::make($request->all(), [
            'center_id' => 'required|integer|exists:donation_centers,id',
            'blood_type' => ['required'],
            'date' => [
                'required',
                // 'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::tomorrow())) {
                        $fail('The appointment date must be at least tomorrow.');
                    }
                },
            ],
            'time' => 'required|date_format:H:i',
        ], [
            'center_id.exists' => 'The donation center does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $center = DonationCenter::findOrFail($request->input('center_id'));

        $availableTimes = $this->getTimes($center, $request->input('date'));

        if (!in_array($request->input('time'), $availableTimes)) {
            return $this->validationErrors(['general' => ['choosed time is not availalbe']]);
        }

        $date = Carbon::parse($request->input('date'));
        $appointment = Appointment::where('center_id', $center->id)
            ->whereDate('date', $date)->latest('time')->first();
        //        if ($appointment && Carbon::parse($appointment->time)->addMinutes(20)
        //                ->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
        //            return $this->validationErrors(['error' => 'No available appointments after 4 PM']);
        //        }
        // $nextAvailableTime = $this->getNextAvailableTime($appointment);

        // if ($nextAvailableTime == null)
        //     return $this->responseError('you cannot take an appointment at this day');

        $time = Carbon::parse($request->input('time'));
        $newAppointment = $this->createNewAppointment(
            $user,
            $center,
            $date,
            $time,
            $request->input('blood_type')
        );

        return $this->successResponse(
            ['appointment' => $newAppointment],
            "Appointment added on " . $date->format('l, F jS, Y') . " at " .
                $time->format('g:i A')
        );
    }
    private function createNewAppointment($user, DonationCenter $center, Carbon $date, Carbon $time, $blood_type)
    {
        return $user->appointments()->create([
            'center_id' => $center->id,
            'status' => 'scheduled',
            'date' => $date->format('Y-m-d'),
            'time' => $time->format('H:i:s'),
            'blood_type' => $blood_type
        ]);
    }
    // private function getNextAvailableTime(?Appointment $appointment)
    // {
    //     if (!$appointment) {
    //         return Carbon::createFromTime(8, 0, 0); //at 8 PM
    //     }

    //     $appointmentTime = Carbon::parse($appointment->time);
    //     $nextAvailableTime = $appointmentTime->addMinutes(20);

    //     if ($nextAvailableTime->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
    //         return null;
    //     }

    //     return $nextAvailableTime;
    // }
    private function canScheduleAppointment(Social|User $user): ?string
    {
        $now = Carbon::now();
        $twoMonthsFromNow = $now->copy()->addMonths(2);

        if ($user->appointments()
            ->scheduled()
            ->whereBetween('date', [$now, $twoMonthsFromNow])
            ->exists()
        ) {
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
        return AppointmentResource::make(Appointment::findOrFail($id));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = \request()->user();


        $appointment = $user->appointments()->find($id);

        if (!$appointment) {
            return $this->responseError('the appointment doesnt exists');
        }

        if ($appointment->status == "complete") {
            return $this->responseError('the appointment is  complete and caanot editted');
        }

        $validator = Validator::make($request->all(), [
            'center_id' => 'required|integer|exists:donation_centers,id',
            'blood_type' => ['required'],
            'date' => [
                'required',
                // 'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::tomorrow())) {
                        $fail('The appointment date must be at least tomorrow.');
                    }
                },
            ],
            'time' => 'required|date_format:H:i',
        ], [
            'center_id.exists' => 'The donation center does not exist.',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $center = DonationCenter::findOrFail($request->input('center_id'));

        $availableTimes = $this->getTimes($center, $request->input('date'), $appointment);

        if (!in_array($request->input('time'), $availableTimes)) {
            return $this->validationErrors(['general' => ['choosed time is not availalbe']]);
        }

        $date = Carbon::parse($request->input('date'));
        $appointmentAtDate = Appointment::where('center_id', $center->id)
            ->whereDate('date', $date)->latest('time')->first();
        //        if ($appointmentAtDate && Carbon::parse($appointmentAtDate->time)->addMinutes(20)
        //                ->greaterThanOrEqualTo(Carbon::createFromTime(16, 0, 0))) {
        //            return $this->validationErrors(['error' => 'No available appointments after 4 PM']);
        //        }
        // $nextAvailableTime = $this->getNextAvailableTime($appointmentAtDate);
        // if ($nextAvailableTime == null)
        //     return $this->responseError('you cannot take an appointment at this day');

        $time = Carbon::parse($request->input('time'));
        $updatedAppointment = $appointment->update([
            'center_id' => $request->input('center_id'),
            'blood_type' => $request->input('blood_type'),
            'date' => $request->input('date'),
            'time' => $time
        ]);

        return $this->successResponse(
            ['appointment' => $appointment],
            "Appointment update successfully"
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = request()->user();

        $appointment = $user->appointments()->scheduled()->find($id);
        if (!$appointment) {
            return $this->responseError('appointment is completed and cannot deleted');
        }
        $appointment->delete();
        return $this->successResponse([], 'appointment deleted successfully');
    }



    public function getAvailableTimes(Request $request): array
    {
        $request->validate([
            'date' => 'required|date',
            'center_id' => 'required|integer|exists:donation_centers,id',
            'appointment_id' => 'nullable|integer|exists:appointments,id',
        ]);

        $date = Carbon::parse($request->input('date'))->format('Y-m-d');

        $appointments = Appointment::where(
            [
                'date' => $date,
                'center_id' => $request->input('center_id'),
            ]
        )->when($request->input('appointment_id'), function ($q) use ($request) {
            $q->where('id', '!=', $request->input('appointment_id'));
        })
            ->pluck('time')
            ->map(function ($time) {
                return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
            })
            ->toArray();
        $times = [];

        $startTime = Carbon::createFromTime(8, 0, 0);
        $endTime = Carbon::createFromTime(18, 0, 0);

        while ($startTime < $endTime) {
            $formattedTime = $startTime->format('H:i');

            if (!in_array($formattedTime, $appointments)) {
                $times[] = $formattedTime;
            }

            $startTime = $startTime->addMinutes(30);
        }

        return $times;
    }

    private function getTimes(DonationCenter $center, string $date, ?Appointment $appointment = null): array
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $appointments = Appointment::where(['date' => $date, 'center_id' => $center->id])
            ->when($appointment != null, function ($q) use ($appointment) {
                $q->where('id', '!=', $appointment->id);
            })
            ->pluck('time')
            ->map(function ($time) {
                return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
            })

            ->toArray();
        $times = [];

        $startTime = Carbon::createFromTime(8, 0, 0);
        $endTime = Carbon::createFromTime(18, 0, 0);

        while ($startTime < $endTime) {
            $formattedTime = $startTime->format('H:i');

            if (!in_array($formattedTime, $appointments)) {
                $times[] = $formattedTime;
            }

            $startTime = $startTime->addMinutes(30);
        }

        return $times;
    }
}
