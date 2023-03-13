<?php

namespace App\Http\Livewire\Admin;

use App\Models\Admin;
use App\Models\Profile;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = "";
    public \App\Models\Profile $profile;
    public string $avatar = "";

    public function render()
    {
        $this->profile = auth()->guard('admin')->user()->profile();
        return view('livewire.admin.profile',[
            'profile' => $this->profile
        ]);
    }

    public function updateProfile(){
        $this->validate([
            'location' => 'nullable|string|min:2',
            'bio' => 'required|string|min:10'
        ]);

        auth()->guard('admin')->user()->profile()
            ->updateOrCreate(['admin_id' => auth()->guard('admin')->user()->id],[
           'location' => $this->location,
            'bio' => $this->bio
        ]);

        $this->alert('success','profile updated successfully');
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }

}
