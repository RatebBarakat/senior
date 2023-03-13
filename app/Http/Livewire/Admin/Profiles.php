<?php

namespace App\Http\Livewire\Admin;

use App\Models\Admin;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profiles extends Component
{
    use WithFileUploads;

    public string $name = "";
    public string $location = "";
    public string $bio = "";
    public \App\Models\Profile $profile;
    public $oldAvatar;
    public $newAvatar;

    public function mount()
    {
        $id = auth()->guard('admin')->user()->id;
        if (!Profile::where('admin_id',$id)->exists()){
            Profile::create(['admin_id' => $id]);
        }
        $this->name = auth()->guard('admin')->user()->name;
        $this->profile = Profile::where('admin_id',$id)->first();

        $this->location = $this->profile->location ? $this->profile->location : '';
        $this->bio = $this->profile->bio ? $this->profile->bio : '';
    }


    public function render()
    {
        $this->oldAvatar = $this->profile->avatar;
        return view('livewire.admin.profile',[
            'profile' => $this->profile
        ]);
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'location' => 'nullable|string|min:2',
            'bio' => 'required|string|min:10',
            'newAvatar' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $data = [
            'location' => $this->location,
            'bio' => $this->bio,
        ];

        if (!empty($this->newAvatar)) {
            $newAvatar = $this->newAvatar->store('avatars', 'public');
            $data['avatar'] = $newAvatar;

            if (!empty($this->oldAvatar)) {
                try {
                    unlink('storage/'.$this->oldAvatar);
                } catch (\Throwable $th) {
                    abort(500);
                }
            }
        }

        $this->profile->update($data);
        auth()->guard('admin')->user()->update(['name' => $this->name]);
        $this->dispatchBrowserEvent('update-name',[
            'name' => auth()->guard('admin')->user()->name
        ]);
        $this->alert('success', 'Profile updated successfully.');
    }

    public function alert(string $type,string $message){
        $this->dispatchBrowserEvent('alert',[
            'type' => $type,
            'message' => $message
        ]);
    }

}
