<?php

namespace App\Http\Livewire\Admin\BloodRequest;

use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        abort_if(Gate::denies('manage-blood-requests'),403);
    }

    public function render()
    {
        $availableDonations = Donation::where(function ($q) {
            $q->where('taken',0)
            ->where('center_id', auth()->guard('admin')->user()->center_id);
        })->get();
    
        $availableDonationsByType = $availableDonations->groupBy('blood_type');

        $sumAvailableByType = [];
        
        foreach ($availableDonationsByType as $bloodType => $donations) {
            $sumAvailableByType[$bloodType] = $donations->sum('quantity');
        }
        
        auth()->guard('admin')->user()->load(
            ['bloodRequests' => function ($query) {
                $query->where('status', 'pending');
            }]
        );
        $bloodRequests = auth()->guard('admin')->user()->bloodRequests;
        return view('livewire.admin.blood-request.index',compact('bloodRequests','sumAvailableByType'));
    }
}
