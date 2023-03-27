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

            $expireBlood = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_expire'))
                ->where('expire_at', '<', Carbon::now()->format('y-m-d'))
                ->groupBy('blood_type')
                ->get();
    
            $nonExpireBlood = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_non_expire'))
                ->where('expire_at', '>=', Carbon::now()->format('y-m-d'))
                ->groupBy('blood_type')
                ->get();

            $bloodByType = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_quantity'))
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

            $expireBlood = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_expire'))
                ->where('expire_at', '<', Carbon::now()->format('y-m-d'))
                ->whereIn('center_id', function($query) use ($user,$centerId) {
                    $query->select('id')
                        ->from('donation_centers')
                        ->where('id', $centerId);
                })
                ->groupBy('blood_type')
                ->get();
    
            $nonExpireBlood = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_non_expire'))
                ->where('expire_at', '>=', Carbon::now()->format('y-m-d'))
                ->whereIn('center_id', function($query) use ($user,$centerId) {
                    $query->select('id')
                        ->from('donation_centers')
                        ->where('id', $centerId);
                })
                ->groupBy('blood_type')
                ->get();

            $bloodByType = Donation::select('blood_type', DB::raw('SUM(quantity) AS total_quantity'))
                ->whereIn('center_id', function($query) use ($user,$centerId) {
                    $query->select('id')
                        ->from('donation_centers')
                        ->where('id', $centerId);
                })    
                ->groupBy('blood_type')
                ->get();
        }
  

        
        $query = DB::getQueryLog();
        return view('admin.dashboard',compact('donationsWeek','donationsMounth','expireBlood', 'nonExpireBlood','bloodByType'));
    
    }
}