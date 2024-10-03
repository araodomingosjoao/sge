<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllocationResource extends JsonResource
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
            'class' => ClassResource::make($this->class),
            'discipline' => DisciplineResource::make($this->discipline),
            'teacher' => TeacherResource::make($this->teacher),
        ];
    }
}
