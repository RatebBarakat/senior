<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];

        $data['id'] = $this->id;
        $data['status'] = $this->status;
        $data['date'] = $this->date;
        $data['blood_type'] = $this->blood_type;
        $data['time'] = Carbon::parse($this->time)->format('H:i');
        $data['quantity'] = $this->quantity;
        $data['center'] = CenterResource::make($this->center);
        $data['location'] = LocationResource::make($this->center->location);

        return $data;
    }
}
