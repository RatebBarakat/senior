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
        $address = $request->input('address');

        $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" .
            urlencode($address) . "&key=" . env('GOOGLE_MAPS_API_KEY');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $geocodeUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geocodeResponse = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $locationData = [
            'name' => $request->input('name'),
            'address' => $address,
            'latitude' => $geocodeResponse['results'][0]['geometry']['location']['lat'],
            'longitude' => $geocodeResponse['results'][0]['geometry']['location']['lng'],
        ];

        Location::create($locationData);

        return redirect()->route('locations.index');
    }

}
