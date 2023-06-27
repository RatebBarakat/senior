<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BloodRequestResourse extends JsonResource
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
        $data['patient_name'] = $this->patient_name;
        $data['hospital_name'] = $this->hospital_name;
        $data['hospital_location'] = $this->hospital_location;
        $data['contact_name'] = $this->contact_name;
        $data['contact_phone_number'] = $this->contact_phone_number;
        $data['blood_type_needed'] = $this->blood_type_needed;
        $data['quantity_needed'] = $this->quantity_needed;
        $data['urgency_level'] = $this->urgency_level;
        $data['status'] = $this->status;
        $data['center'] = CenterResource::make($this->center);

        return $data;  
    }
}
