<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('role');
        if ($user->isSuperAdmin()) {
            $donationsWeek = Donation::with('center')
                ->select('blood_type', DB::raw('SUM(quantity) AS total_donated'))
                ->whereBetween('date',[Carbon::now()->subWeek(),Carbon::now()])
                ->groupBy('blood_type')
                ->get();
    
            $donationsMounth = Donation::with('center')
                ->select('blood_type', DB::raw('SUM(quantity) AS total_donated'))
                ->whereBetween('date',[Carbon::now()->subMonth(),Carbon::now()])
                ->groupBy('blood_type')
                ->get();
        } else {

        
            DB::enableQueryLog();
            
            $centerId = $user->role->name == "center-admin" ? $user->center->id : $user->center_id;

            $donationsWeek = Donation::with('center')
                ->select('blood_type', DB::raw('SUM(quantity) AS total_donated'))
                ->whereIn('center_id', function($query) use ($user,$centerId) {
                    $query->select('id')
                        ->from('donation_centers')
                        ->where('id', $centerId);
                })
                ->whereBetween('date',[Carbon::now()->subWeek(),Carbon::now()])
                ->groupBy('blood_type')
                ->get();
    
            $donationsMounth = Donation::with('center')
                ->select('blood_type', DB::raw('SUM(quantity) AS total_donated'))
                ->whereIn('center_id', function($query) use ($user,$centerId) {
                    $query->select('id')
                        ->from('donation_centers')
                        ->where('id', $centerId);
                })
                ->whereBetween('date',[Carbon::now()->subMonth(),Carbon::now()])
                ->groupBy('blood_type')
                ->get();
        }
        $query = DB::getQueryLog();
        return view('admin.dashboard',compact('donationsWeek','donationsMounth','query'));
    }
    
}
