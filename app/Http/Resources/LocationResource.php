<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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

        $data['name'] = $this->name;
        $data['city'] = $this->city;
        $data['latitude'] = $this->latitude;
        $data['longitude'] = $this->longitude;

        return $data;    }
}
