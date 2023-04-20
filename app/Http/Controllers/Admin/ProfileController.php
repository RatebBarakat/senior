<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){//return auth profile
        return view('admin.profile');
    }

    public function viewProfile($id){// show an admin profile
        $admin = Admin::with('profile')->findOrFail($id);
        return view('admin.profile-show',[
           'admin' => $admin
        ]);
    }
}
