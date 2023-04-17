<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ResetBloodRequestLockJob;
use App\Models\BloodRequest;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use SysvSemaphore;

class BloodRequestController extends Controller
{
    public function show(int $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
    
        $AvailableBLoodArray = $this->checkAvailableBlood($bloodRequest);
        $AvailableBLood = $AvailableBLoodArray['donations'];
        if (count($AvailableBLoodArray['donations']) <= 0) {
            $this->resetLockedBy($bloodRequest);
            return redirect('/admin')->with('error', 'There are no available donations at your center.');
        }elseif ($AvailableBLoodArray['sum_available'] < $bloodRequest->quantity_needed) {
            $this->resetLockedBy($bloodRequest);
            return redirect('/admin')->with('error', 'There are no enouth quantity
             donations at your center.');
        }
    
        if (is_null($bloodRequest->locked_by)) {
            DB::beginTransaction();
    
            $userId = auth()->guard('admin')->user()->id;
    
            $bloodRequest->locked_by = $userId;
            $bloodRequest->save();
    
            dispatch(new ResetBloodRequestLockJob($bloodRequest))
                ->delay(now()->addMinutes(30));
    
            DB::commit();
    
            return view('admin.blood-request.blood-request-show', compact('bloodRequest','AvailableBLood'));
        } else if ($bloodRequest->locked_by === auth()->guard('admin')->user()->id) {
            return view('admin.blood-request.blood-request-show', compact('bloodRequest','AvailableBLood'));
        } else {
            abort(403, 'This blood request is taken by another admin');
        }
    
    }

    public function unLock(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer|exists:blood_requests'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }
        $bloodRequest = BloodRequest::findOrFail($request->input('id'));
        if ($bloodRequest->locked_by && $bloodRequest->locked_by == auth()->guard('admin')->user()->id) {
            $bloodRequest->locked_by = null;
            $bloodRequest->save();
            return response()->json(['message' => 'blood request unlocked'],200);
        }
        return response()->json([
            'message' => 'you cannot unlock this blood request',
            'url' => url('/admin')
        ],200);
    }
    

    private function resetLockedBy(BloodRequest $bloodRequest)
    {
        $bloodRequest->locked_by = null;
        $bloodRequest->save();
    }

    private function checkAvailableBlood(BloodRequest $bloodRequest)
    {
        $donations = Donation::where(function ($q) use ($bloodRequest) {
            $q->where('taken',0)
            ->where('center_id', auth()->guard('admin')->user()->center_id)
            ->where('blood_type', $bloodRequest->blood_type_needed);
        })->get();
    
        $sumAvailable = $donations->sum('quantity');
    
        return [
            'donations' => $donations,
            'sum_available' => $sumAvailable
        ];
    }
    
}
