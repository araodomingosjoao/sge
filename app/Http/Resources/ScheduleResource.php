<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'class_id' => $this->class->id,
            'class_name' => $this->class->name,
            'discipline_id' => $this->discipline->id,
            'discipline_name' => $this->discipline->name,
            'teacher_id' => $this->teacher->id,
            'teacher_first_name' => $this->teacher->user->first_name,
            'teacher_last_name' => $this->teacher->user->last_name,
            'teacher_email' => $this->teacher->user->email,
            'teacher_phone' => $this->teacher->user->phone,
            'teacher_profile_picture_path' => $this->teacher->user->profile_picture_path,
            'classroom_id' => $this->classroom->id,
            'classroom_name' => $this->classroom->name,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
    }
}
