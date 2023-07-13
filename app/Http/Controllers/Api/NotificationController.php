<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseApi;

class NotificationController extends Controller
{
    use ResponseApi;
    public function index() {
        try {
            $user = request()->user();

        if(request()->has('type') && request()->get('type') == "unread"){
            $user->load('unreadNotifications');
            return $this->successResponse([$user->unreadNotifications]);
        }
        $user->load('notifications');
        return $this->successResponse([$user->notifications]);  
        } catch (\Throwable $q) {
            return $this->responseError($q->getMessage());
        }      
    }
}
