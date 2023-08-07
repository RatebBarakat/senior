<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResourse;
use App\Models\Event;
use App\Traits\ResponseApi;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ResponseApi;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function upcomingEvent() {
        return EventResourse::collection(Event::where('start_date', '>', now())->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
