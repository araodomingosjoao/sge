<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'profile_picture_path' => $this->user->profile_picture_path,
            'status' => $this->user->status,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'case_number' => $this->case_number,
            'observation' => $this->observation,
            'school_type_education_id' => $this->user->school->type_education_id,
            'school_name' => $this->user->school->school_name,
            'school_logo_path' => $this->user->school->logo_path,
        ];
    }
}
