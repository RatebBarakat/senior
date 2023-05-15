<?php

namespace App\Http\Livewire\Admin\Super;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class CreateEvent extends Component
{
    public function mount()
    {
        abort_if(Gate::denies('manage-events'),403);
    }

    public function render()
    {
        return view('livewire.admin.super.create-event');
    }
}
