<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(){
        $locations = Location::get()->toArray();
        return view('admin.location.index',compact('locations'));
    }

    public function create(){
        return view('admin.location.create-location');
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

    public function edit(int $id)
    {
        $location = Location::findOrFail($id);
        return view('admin.location.edit-location',compact('location'));
    }
    
    public function update(Request $request,int $id)
    {
        try {
        $request->validate([
            'city' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::findOrFail($id);

        // Create a new location object
        $location->update([
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
