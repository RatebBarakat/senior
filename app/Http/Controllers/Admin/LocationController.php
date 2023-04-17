<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(){
        return view('admin.location');
    }

    public function create(){
        return view('admin.create-location');
    }

    public function store(Request $request)
    {
        try {
        $request->validate([
            'city' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

    
        // Create a new location object
        Location::create([
            'name' => $request->input('city'),
            'city' => $request->input('city'),
            'latitude' => $request->input('latitude'),
            'longitude' =>  $request->input('longitude')
        ]);
        return response()->json(['success' => true]);

        } catch (\Throwable $th) {
            return response($th->getMessage(),400);
        }
    
    }
    

}
