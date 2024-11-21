<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="LevelStoreRequest",
 *     type="object",
 *     required={"name", "type_education_id", "year"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="type_education_id", type="integer"),
 *     @OA\Property(property="year", type="integer"),
 * )
 */
class LevelStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type_education_id' => 'required|string|exists:type_educations,id',
            'year' => 'required|numeric|unique:levels,year|min:1',
        ];
    }
}
