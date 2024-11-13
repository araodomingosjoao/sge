<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
* @OA\Schema(
 *     schema="ClassResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="course_id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="level_name", type="string")
 * )
 */
class ClassResource extends JsonResource
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
            'course_id' => $this->course_id,
            'name' => $this->name,
            'level_name' => $this->level->name,
        ];
    }
}
