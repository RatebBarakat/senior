<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyAdminsBloodRequest;
use App\Models\BloodRequest;
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

        $bloodRequest = BloodRequest::create($validator->validated());
        dispatch(new NotifyAdminsBloodRequest($bloodRequest));
        return $this->successResponse(['bloodRequest' => $bloodRequest],'request addedd successfully');
    }

    public function show(int $id)
    {
        $blood = BloodRequest::findOrFail($id);
        return $blood->patient_name;
    }
}
