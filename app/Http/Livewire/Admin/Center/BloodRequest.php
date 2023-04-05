<?php

namespace App\Http\Livewire\Admin\Center;

use Livewire\Component;

class BloodRequest extends Component
{
    public function mount()
    {
        if (!auth()->guard('admin')->user()->isEmployee()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.admin.center.blood-request');
    }
}
