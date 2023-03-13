<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){//return auth profile

    }

    public function viewProfile($id){// shoe an admin profile
        $admin = Admin::with('profile')->findOrFail($id);
        return view('admin.profile-show',[
           'profile' => $admin->profile
        ]);
    }
}
