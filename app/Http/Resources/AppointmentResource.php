<?php

namespace App\Http\Resources;

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

        if (!in_array($request->route()->getActionMethod(), ['index', 'show'])) {
            $data['id'] = $this->id;
        }

        $data['status'] = $this->status;
        $data['date'] = $this->date;
        $data['time'] = $this->time;
        $data['center'] = CenterResource::make($this->center);
        $data['location'] = LocationResource::make($this->center->location);

        return $data;
    }
}
