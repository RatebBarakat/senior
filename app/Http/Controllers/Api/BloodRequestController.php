<?php

namespace App\Http\Controllers\Api;

use App\Helpers\NotifyBloodRequestUrgency;
use App\Http\Controllers\Controller;
use App\Http\Resources\BloodRequestResourse;
use App\Jobs\NotifyAdminsBloodRequest;
use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\DonationCenter;
use App\Models\User;
use App\Traits\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    use ResponseApi;

    public function index()
    {
        request()->user()->load('requests');
        return BloodRequestResourse::collection(request()->user()->requests);
    }

    public function store(Request $request)
    {
        $user = request()->user();

        $validator = Validator::make($request->all(), [
            'patient_name' => 'required|string',
            'hospital_name' => 'required|string',
            'hospital_location' => 'required|string',
            'contact_name' => 'nullable|string',
            'contact_phone_number' => ['required', 'regex:/^[0-9]{8}$/'],
            'blood_type_needed' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'quantity_needed' => 'required',
            'urgency_level' => ['required', Rule::in(['immediate', '24 hours'])],
            'center_id' => 'required|integer|exists:donation_centers,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $center = DonationCenter::findOrFail($request->input('center_id'));
        $type = $user instanceof User ? 'user_id' : 'admin_id';
        if ($this->checkCompatibility($center, $request->input('blood_type_needed'), $request->input('quantity_needed'))) {
            $bloodRequest = BloodRequest::create(array_merge(
                $validator->validated(),
                [$type => $user->id]
            ));
            dispatch(new NotifyAdminsBloodRequest($bloodRequest));
            return $this->successResponse(['bloodRequest' => $bloodRequest], 'request addedd successfully');
        } //else check for centers if exists another

        $center = $this->getAvailableCenter($request->input('blood_type_needed'), $request->input('quantity_needed'));

        if (is_null($center)) {
            $bloodRequest = BloodRequest::create(array_merge(
                $validator->validated(),
                [$type => $user->id]
            ));
            
            NotifyBloodRequestUrgency::notifyUsers($bloodRequest);
            
            return $this->successResponse([], "the requested quantity is not availalbe we send an email to users for donate");
    
        } else {
            return response()->json([
                'message' => 'selected center dont have enough quanatity of this blood type this is an availalbe one',
                'availabe_center' => $center
            ], 412);
        }
    }
    public function show(int $id)
    {
        $blood = request()->user()->requests()->findOrFail($id);
        return BloodRequestResourse::make($blood);
    }

    public function update(Request $request, int $id)
    {
        $bloodRequest = request()->user()->requests()->find($id);

        if (!$bloodRequest) {
            return $this->responseError('the request was not found');
        }

        if ($bloodRequest->status != "pending") {
            return $this->responseError("you cannot update this request with status {$bloodRequest->status}");
        }

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'patient_name' => 'required|string',
            'hospital_name' => 'required|string',
            'hospital_location' => 'required|string',
            'contact_name' => 'nullable|string',
            'contact_phone_number' => ['required', 'regex:/^[0-9]{8}$/'],
            'blood_type_needed' => ['required', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'quantity_needed' => 'required',
            'urgency_level' => ['required', Rule::in(['immediate', '24 hours'])],
            'center_id' => 'required|integer|exists:donation_centers,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $center = DonationCenter::findOrFail($request->input('center_id'));

        $type = $user instanceof User ? 'user_id' : 'admin_id';

        if ($this->checkCompatibility($center, $request->input('blood_type_needed'), $request->input('quantity_needed'))) {
            $bloodRequest->update(
                array_merge($validator->validated(), [$type => $user->id])
            );
            // dispatch(new NotifyAdminsBloodRequest($bloodRequest));
            return $this->successResponse(['bloodRequest' => $bloodRequest], 'request updated successfully');
        }

        $center = $this->getAvailableCenter($request->input('blood_type_needed'), $request->input('quantity_needed'));

        if (is_null($center)) {
            $bloodRequest->update(
                array_merge($validator->validated(), [$type => $user->id])
            );
            NotifyBloodRequestUrgency::notifyUsers($bloodRequest);
            
            return $this->successResponse([], "the requested quantity is not availalbe we send an email to users for donate");
        } else {
            return response()->json([
                'message' => 'selected center dont have enough quanatity of this blood type',
                'availabe_center' => $center
            ], 412);
        }
    }

    public function destroy(int $id)
    {
        $bloodRequest = request()->user()->requests()->findOrFail($id);
        if ($bloodRequest->status == "pending") {
            $bloodRequest->delete();
            return $this->successResponse([], 'blood request deleted successfully');
        } else {
            return $this->responseError("you cannot delete this request with status {$bloodRequest->status}");
        }
    }
    private function checkCompatibility(DonationCenter $center, $bloodTypeNeeded, $quantityNeeded)
    {
        $availableQuantity = Donation::where([
            ['blood_type', $bloodTypeNeeded],
            ['expire_at','>', now()],
            ['center_id', $center->id],
            ['taken',0]
        ])->sum('quantity');

        if ($quantityNeeded > $availableQuantity) {
            return false;
        }

        return true;
    }

    private function getAvailableCenter($bloodTypeNeeded, $quantityNeeded)
    {
        $center = DonationCenter::whereHas('donations', function ($query) use ($bloodTypeNeeded, $quantityNeeded) {
            $query->where('blood_type', $bloodTypeNeeded)
                ->where('taken', 0)
                ->where('expire_at','>',now())
                ->havingRaw('SUM(quantity) >= ?', [$quantityNeeded]);
        })->first();        

        return $center ?? null;
    }
}
