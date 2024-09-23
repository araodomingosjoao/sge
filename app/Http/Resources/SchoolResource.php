<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type_education_id' => $this->type_education_id,
            'name' => $this->name,
            'logo' => $this->logo,
            'address' => $this->address,
        ];
    }
}
