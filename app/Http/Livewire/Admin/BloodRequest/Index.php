<?php

namespace App\Http\Livewire\Admin\BloodRequest;

use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    public int $filter = 0;
    public bool $canComplete = false;
    public string $urgencyLevel = "";
 
    protected $queryString = [
        'filter' => ['except' => 0],
    ];
    public function mount()
    {
        abort_if(Gate::denies('manage-blood-requests'),403);
    }

    public function render()
    {
        $adminCenterId = auth()->guard('admin')->user()->center_id;
        $availableDonations = Donation::where([
                ['taken', '=', 0],
                ['center_id', '=', $adminCenterId]
            ])->get();
        
        $availableDonationsByType = $availableDonations->groupBy('blood_type');
        
        $sumAvailableByType = $availableDonationsByType->map(fn($donations) => $donations->sum('quantity'));
        
        $admin = auth()->guard('admin')->user();
        $admin->load(['bloodRequests' => function ($query) {
            $query->where('status', 'pending');
        }]);
        $id = request()->query('filter');
        $bloodRequestsQuery = $admin->bloodRequests();
        
        if ($id) {
            $this->filter = $id;
            $bloodRequestsQuery->where('id', $id);
        }
        
        $bloodRequests = $bloodRequestsQuery
            ->when($this->urgencyLevel != "", function ($query) {
                $query->where('urgency_level', $this->urgencyLevel);
            })
            ->when($this->filter != 0,function ($q)
            {
                $q->where('id',$this->filter);
            })
            ->when($this->canComplete, function ($query) use ($sumAvailableByType) {
                $query->where(function ($subquery) use ($sumAvailableByType) {
                    $sumAvailableByType->each(function ($quantity, $bloodType) use ($subquery) {
                        $subquery->orWhere(function ($q) use ($bloodType, $quantity) {
                            $q->where('blood_type_needed', $bloodType)
                                ->where('quantity_needed', '<=', $quantity);
                        });
                    });
                });
            })            
            ->get();
        
        return view('livewire.admin.blood-request.index', compact('bloodRequests', 'sumAvailableByType'));
    }
    
    
}
