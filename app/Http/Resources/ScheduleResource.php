<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ScheduleResource",
 *     @OA\Property(property="id", type="integer", description="ID of the schedule"),
 *     @OA\Property(property="class_id", type="integer", description="ID of the class"),
 *     @OA\Property(property="class_name", type="string", description="Name of the class"),
 *     @OA\Property(property="discipline_id", type="integer", description="ID of the discipline"),
 *     @OA\Property(property="discipline_name", type="string", description="Name of the discipline"),
 *     @OA\Property(property="teacher_id", type="integer", description="ID of the teacher"),
 *     @OA\Property(property="teacher_first_name", type="string", description="First name of the teacher"),
 *     @OA\Property(property="teacher_last_name", type="string", description="Last name of the teacher"),
 *     @OA\Property(property="teacher_email", type="string", format="email", description="Email of the teacher"),
 *     @OA\Property(property="teacher_phone", type="string", description="Phone number of the teacher"),
 *     @OA\Property(property="teacher_profile_picture_path", type="string", nullable=true, description="Path to the teacher's profile picture"),
 *     @OA\Property(property="classroom_id", type="integer", description="ID of the classroom"),
 *     @OA\Property(property="classroom_name", type="string", description="Name of the classroom"),
 *     @OA\Property(property="day", type="string", description="Day of the schedule"),
 *     @OA\Property(property="start_time", type="string", format="time", description="Start time of the schedule"),
 *     @OA\Property(property="end_time", type="string", format="time", description="End time of the schedule")
 * )
 */
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
