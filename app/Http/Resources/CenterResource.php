<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CenterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private $data = [];
    public function toArray($request) : array
    {
        $data = [];

        if (!in_array($request->route()->getActionMethod(), ['index', 'show'])) {
            $data['id'] = $this->id;
        }

        $data['name'] = $this->name;

        return $data;
    }

}
