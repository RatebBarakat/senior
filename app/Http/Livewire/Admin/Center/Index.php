<?php

namespace App\Http\Livewire\Admin\Center;

use Livewire\Component;

class Index extends Component
{

    public function render()
    {
        auth()->guard()->user()->load('center');
        $center = auth()->guard()->user()->center;
        $center->load('employees');
        return view('livewire.admin.center.index',[
            'center' => $center,
            'employees' => $center->employees
        ]);
    }
}
