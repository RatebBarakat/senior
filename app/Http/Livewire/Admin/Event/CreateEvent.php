<?php

namespace App\Http\Livewire\Admin\Event;

use App\Models\Admin;
use App\Models\BloodType;
use App\Models\Message;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateEvent extends Component
{
    public $bloodType = '';
    public $quantity = '';

    public function mount()
    {
        abort_if(Gate::denies('request-event'),404);
    }

    public function render()
    {
        return view('livewire.admin.event.create-event',[
            'bloodTypes' => BloodType::get()
        ]);
    }

    public function requestEvent()
    {
        $this->validate([
            'bloodType' => ['required',Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'quantity' => 'required|integer|min:0|max:20'
        ]);

        $message = "we need and event for {$this->bloodType} for donating a quantity of {$this->quantity} liters.";
        $superAdmin = Admin::whereHas('role', function ($q)
        {
            $q->where('name','super-admin');
        })->first();

        Message::create([
            'body' => $message,
            'sender_type' => get_class(auth()->guard('admin')->user()),
            'sender_id' => auth()->guard('admin')->user()->id,
            'recipient_type' => get_class($superAdmin),
            'recipient_id' => $superAdmin->id,
            'message_type' => 'request-event'
        ]);

        $this->alert('success','messages sended to admin successfully');


    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }
    
}
