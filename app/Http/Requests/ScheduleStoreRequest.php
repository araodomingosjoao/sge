<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ScheduleStoreRequest
 *
 * This request class validates the input data when creating a new schedule.
 * It ensures that all required fields are provided and adhere to specific formats and constraints.
 *
 * @OA\Schema(
 *     schema="ScheduleStoreRequest",
 *     required={
 *         "teacher_id",
 *         "class_id",
 *         "discipline_id",
 *         "classroom_id",
 *         "day",
 *         "start_time",
 *         "end_time"
 *     },
 *     @OA\Property(
 *         property="teacher_id",
 *         type="integer",
 *         description="ID of the teacher, must exist in the teachers table"
 *     ),
 *     @OA\Property(
 *         property="class_id",
 *         type="integer",
 *         description="ID of the class, must exist in the classes table"
 *     ),
 *     @OA\Property(
 *         property="discipline_id",
 *         type="integer",
 *         description="ID of the discipline, must exist in the disciplines table"
 *     ),
 *     @OA\Property(
 *         property="classroom_id",
 *         type="integer",
 *         description="ID of the classroom, must exist in the classrooms table"
 *     ),
 *     @OA\Property(
 *         property="day",
 *         type="string",
 *         description="Day of the schedule, must be one of Monday, Tuesday, Wednesday, Thursday, Friday, or Saturday",
 *         enum={"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"}
 *     ),
 *     @OA\Property(
 *         property="start_time",
 *         type="string",
 *         format="time",
 *         description="Start time of the schedule in HH:MM format"
 *     ),
 *     @OA\Property(
 *         property="end_time",
 *         type="string",
 *         format="time",
 *         description="End time of the schedule in HH:MM format, must be after the start time"
 *     )
 * )
 */
class ScheduleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'teacher_id'    => 'required|exists:teachers,id',
            'class_id'      => 'required|exists:classes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'day'           => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ];
    }
}
