<?php

namespace App\Http\Resources\School;

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
            'school_name' => $this->school_name,
            'logo_path' => $this->logo_path,
            'status' => $this->status,
        ];
    }
}
