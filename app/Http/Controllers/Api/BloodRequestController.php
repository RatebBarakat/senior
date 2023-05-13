<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyAdminsBloodRequest;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\DonationCenter;
use App\Traits\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    use ResponseApi;

    public function index()
    {
        return request()->user()->load('requests');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'patient_name' => 'required|string',
            'hospital_name' => 'required|string',
            'hospital_location' => 'required|string',
            'contact_name' => 'nullable|string',
            'contact_phone_number' => 'required|integer',
            'blood_type_needed' => ['required',Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'quantity_needed' => 'required',
            'urgency_level' => ['nullable',Rule::in(['immediate','24 hours'])],
            'center_id' => 'required|integer|exists:donation_centers,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $center = DonationCenter::findOrFail($request->input('center_id'));

        if ($this->checkCompatibility($center,$request->input('blood_type_needed'),$request->input('quantity_needed'))) {
            $bloodRequest = BloodRequest::create($validator->validated());
            dispatch(new NotifyAdminsBloodRequest($bloodRequest));
            return $this->successResponse(['bloodRequest' => $bloodRequest],'request addedd successfully');
        }
        
        $center = $this->getAvailableCenter($request->input('blood_type_needed'),$request->input('quantity_needed'));

        if (is_null($center)) {
            return response()->json([
                'error' => "there no center have {$request->input('blood_type_needed')} of type {$request->input('quantity_needed')}"
            ]);
        } else {
            return response()->json([
                'message' => 'selected center dont have enough quanatity of this blood type',
                'availabe-center' => $center
            ]);
        }
        
    }

    private function checkCompatibility(DonationCenter $center,$bloodTypeNeeded,$quantityNeeded)
    {
        $availableQuantity = Donation::where([
            ['blood_type','=',$bloodTypeNeeded],
            ['center_id','=',$center->id],
        ])->sum('quantity');
        
        if ($quantityNeeded > $availableQuantity) {
            return false;
        }

        return true;
    }

    private function getAvailableCenter($bloodTypeNeeded,$quantityNeeded)
    {
        $center = DonationCenter::whereHas('donations', function ($query) use ($bloodTypeNeeded,$quantityNeeded) {
            $query->where([
                ['blood_type', $bloodTypeNeeded],
            ])->havingRaw('SUM(quantity) > ?', [$quantityNeeded]);
        })->first();

        return $center ?? null;
    }

    public function show(int $id)
    {
        $blood = BloodRequest::findOrFail($id);
        return $blood->patient_name;
    }
}
