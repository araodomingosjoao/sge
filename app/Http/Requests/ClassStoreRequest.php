<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
* @OA\Schema(
 *     schema="ClassStoreRequest",
 *     type="object",
 *     required={"name", "level_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="course_id", type="integer"),
 *     @OA\Property(property="level_id", type="integer")
 * )
 */
class ClassStoreRequest extends FormRequest
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
            'school_id' => 'required|string|exists:schools,id',
            'name' => 'required|string|max:255',
            'course_id' => 'nullable|string|exists:courses,id',
            'level_id' => 'required|string|exists:levels,id',
        ];
    }
}
