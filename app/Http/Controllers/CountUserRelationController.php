<?php

namespace App\Http\Controllers;

use App\Traits\ResponseApi;
use Illuminate\Http\Request;

class CountUserRelationController extends Controller
{
    use ResponseApi;
    public function index(){
        $user = request()->user();
        $user->loadCount('appointments','requests');
        return $this->successResponse([
            'appointments_count' => $user->appointments_count,
            'requests_count' => $user->requests_count,
        ]);
    }
}
