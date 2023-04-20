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
            ->when($this->canComplete && !$sumAvailableByType->isEmpty(), function ($query) use ($sumAvailableByType) {
                $query->where(function ($subquery) use ($sumAvailableByType) {
                    $sumAvailableByType->each(function ($quantity, $bloodType) use ($subquery) {
                        $subquery->orWhere(function ($q) use ($bloodType, $quantity) {
                            $availabeTypes = $this->getAvailableBloodGroups($bloodType);//array of compatable types with specific type $bloodType
                            $q->whereIn('blood_type_needed', $availabeTypes)//filter by avaialbe types
                                ->where('quantity_needed', '<=', $quantity);//check quantity
                        });
                    });
                });
            })    
            ->when($this->canComplete && $sumAvailableByType->isEmpty(), function ($query) {
                $query->whereRaw('false');
            })        
            ->get();
        
        return view('livewire.admin.blood-request.index', compact('bloodRequests', 'sumAvailableByType'));
    }
    
    private function getAvailableBloodGroups($bloodTypeNeeded)
    {
        $sign = $bloodTypeNeeded[-1];

        $position = strpos($bloodTypeNeeded, $sign);

        $bloodType = substr($bloodTypeNeeded, 0, $position);

        $availabeBloodGroups = array(
            'A' => array('A', 'AB'),
            'B' => array('B', 'AB'),
            'AB' => array('AB'),
            'O' => array('A', 'B', 'AB', 'O')
        );

        foreach ($availabeBloodGroups as $type => &$availabe) {
            foreach ($availabe as &$avaiablewithoutsign) {
                $avaiablewithoutsign .= $sign;
            }
        }

        return $availabeBloodGroups[$bloodType];
   
    }
    
}
