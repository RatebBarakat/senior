<?php

namespace App\Http\Livewire\Admin\BloodRequest;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Notifications\BloodRequestCompleted;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BloodRequestEdit extends Component
{
    public BloodRequest $bloodRequest;
    public $totalSelected = 0;
    public $AvailableBLood;
    public array $neededDecrement = [];
    public array $selectedBlood = [];
    public function mount(BloodRequest $bloodRequest,$AvailableBLood)
    {
        if (!$bloodRequest->locked_by === auth()->guard('admin')->user()->id) {
            abort(403);
        }
        $this->bloodRequest = $bloodRequest;
        $this->AvailableBLood = $AvailableBLood;
    }

    public function render()
    {
        return view('livewire.admin.blood-request.blood-request-edit',[
            'bloodRequest' => $this->bloodRequest,
            'availableBlood' => $this->AvailableBLood
        ]);
    }

    public function showCompleteRequest()
    {
        $this->dispatchBrowserEvent('show-complete-modal');
    }

    public function hideCompleteRequest()
    {
        $this->dispatchBrowserEvent('hide-complete-modal');
    }

    public function addBlood(int $id)
    {
        $total = $this->totalSelected;
        $donation = Donation::where('id', $id)->lockForUpdate()->firstOrFail();
    
        if (in_array($donation->id,$this->selectedBlood)) {
            $index = array_search($donation->id, $this->selectedBlood);

            if ($index !== false) {//remove the donation
                $needDecrement = $this->neededDecrement[0][$donation->id] ?? null;
                $decrement = $needDecrement ?? $donation->quantity;
                $this->totalSelected -= $decrement;
                unset($this->selectedBlood[$index]);
                foreach($this->neededDecrement as $key => $value) {//check for needed devrement if exists remove it
                    if(array_key_exists($donation->id, $value)) {
                        unset($this->neededDecrement[$key]);
                        break;
                    }
                }
            }
            
            // release the lock on the selected donation
            DB::commit();
    
            return;
        }
    

        if ($this->bloodRequest->quantity_needed > $this->totalSelected) {//add the donation
            if ($total + $donation->quantity <= $this->bloodRequest->quantity_needed) {
                $this->totalSelected += $donation->quantity;
            } else {
                $this->totalSelected += ($this->bloodRequest->quantity_needed - $total);
                $quantity_nemained = $this->bloodRequest->quantity_needed - $total;
                array_push($this->neededDecrement,[$donation->id => $quantity_nemained]);
            }
            array_push($this->selectedBlood,$donation->id);
            DB::commit();
        }else {
            $this->alert('error','the needed quantity is comleted');
        }
    
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }
    
    public function completeRequest()
    {
        DB::beginTransaction();
    
        try {
            foreach ($this->selectedBlood as $value => $id) {
                $donation = Donation::where('id', $id)->lockForUpdate()->firstOrFail();
                if ($donation->taken == 0) {//checl if the doantion is used
                    if (isset($this->neededDecrement[0][$donation->id])) {
                        $quantity = $this->neededDecrement[0][$donation->id];
                        if ($donation->quantity >= $quantity) {//checl for quantity availability
                            $donation->quantity -= $quantity;
                            $donation->save();
                            foreach($this->neededDecrement as $key => $value) {
                                if(array_key_exists($donation->id, $value)) {
                                    unset($this->neededDecrement[$key]);
                                    break;
                                }
                            }
                        }else {
                            throw new Exception('quantity not enouth');
                        }
                    } else {
                        $quantity = $donation->quantity;
                        $donation->taken = 1;
                    }
                    $this->bloodRequest->donations()->attach($donation->id, ['quantity_used' => $quantity]);
                    $donation->save();
                } else {
                    throw new Exception('another admin use this blood so you cannot use it');
                }
            }
    
            $this->bloodRequest->status = "fulfilled";
            $this->bloodRequest->locked_by = null;
            $this->bloodRequest->save();
    
            $this->bloodRequest->user->notify(
                new BloodRequestCompleted(auth()->guard('admin')->user(),$this->bloodRequest)
            );
    
            DB::commit();
            $this->hideCompleteRequest();
            return redirect()->route('admin.blood-request.index');
        } catch (\Throwable $th) {
            DB::rollback();
            $this->alert('error',$th->getMessage());
        }
    }
    
}
