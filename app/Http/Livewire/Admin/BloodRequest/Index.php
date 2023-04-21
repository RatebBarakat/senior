<?php

namespace App\Http\Livewire\Admin\BloodRequest;

use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    const CAN_GIVE_TO = [
    'A+' => ['A+', 'AB+'],
    'A-' => ['A-', 'A+', 'AB-', 'AB+'],
    'B+' => ['B+', 'AB+'],
    'B-' => ['B-', 'B+', 'AB-', 'AB+'],
    'AB+' => ['AB+'],
    'AB-' => ['AB-', 'AB+'],
    'O+' => ['O+', 'A+', 'B+', 'AB+'],
    'O-' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']
    ];

    public int $filter = 0;
    public bool $canComplete = false;
    public string $urgencyLevel = "";

    public array $availableBloods = [];
 
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
        
        $sumAvailableDonationByType = $availableDonationsByType
                ->map(fn($donations) => $donations->sum('quantity'));//get the sum availble foreach type


        $this->availableBloods = $this->getSumCanReceiveFrom($sumAvailableDonationByType);
        $admin = auth()->guard('admin')->user();
        $admin->load(['bloodRequests' => function ($query) {
            $query->where('status', 'pending');
        }]);

        $id = request()->query('filter');
        $bloodRequestsQuery = $admin->bloodRequests()->where('status','pending');
        if ($id) {//asign id filtered
            $this->filter = $id;
            $bloodRequestsQuery->where('id', $id);
        }

        $bloodRequests = $bloodRequestsQuery
        ->when($this->urgencyLevel != "", function ($query) {//filter by urgency level
            $query->where('urgency_level', $this->urgencyLevel);
        })
        ->when($this->filter != 0,function ($q)//for view any blood request by id
        {
            $q->where('id',$this->filter);
        })
        ->when($this->canComplete && !$sumAvailableDonationByType->isEmpty(), function ($query) use ($sumAvailableDonationByType) {
            $sumAvailableForBloodGroups = $this->availableBloods;
            $query->where(function ($subquery) use ($sumAvailableForBloodGroups) {
                foreach ($sumAvailableForBloodGroups as $bloodType => $sumAvailable) {//sum available to give
                    $subquery->orWhere(function ($subquery2) use ($bloodType, $sumAvailable) {
                        $subquery2->where('blood_type_needed', $bloodType)
                                  ->where('quantity_needed', '<=', $sumAvailable);
                    });
                }
            });
        })          
        ->when($this->canComplete && $sumAvailableDonationByType->isEmpty(), function ($query) {
            $query->whereRaw('false');
        })        
        ->get();


        return view('livewire.admin.blood-request.index', compact('bloodRequests', 'sumAvailableDonationByType'));
    }
    

    private function getSumCanReceiveFrom($sumAvailableDonationByType)
    {
        $canGiveTo = [
            'A+' => 0,
            'A-' => 0,
            'B+' => 0,
            'B-' => 0,
            'AB+' => 0,
            'AB-' => 0,
            'O+' => 0,
            'O-' => 0
        ];

        $intersect = array_intersect_key(self::CAN_GIVE_TO, $sumAvailableDonationByType->toArray());

        foreach ($intersect as $type => $canReceive) {
            foreach ($canReceive as $canGive) {
                $canGiveTo[$canGive] += $sumAvailableDonationByType[$type];
            }
        }

        return $canGiveTo;
    }


    
}
