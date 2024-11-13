<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StudentStoreRequest",
 *     type="object",
 *     required={"school_id", "first_name", "last_name", "email", "phone", "password", "birth_date"},
 *
 *     @OA\Property(
 *         property="school_id",
 *         type="string",
 *         description="Unique identifier for the school",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     ),
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="First name of the student",
 *         example="John"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Last name of the student",
 *         example="Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Unique email address of the student",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Unique phone number of the student",
 *         example="+1234567890"
 *     ),
 *     @OA\Property(
 *         property="profile_picture_path",
 *         type="string",
 *         format="binary",
 *         description="Profile picture of the student, allowed formats: jpg, jpeg, png, max size: 2048 KB",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="boolean",
 *         description="Status of the student account, active (true) or inactive (false)",
 *         example=true,
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Password for the student, must contain at least 6 characters, including upper and lower case letters, a digit, and a special character",
 *         example="Password@123",
 *     ),
 *     @OA\Property(
 *         property="case_number",
 *         type="string",
 *         description="Optional case number associated with the student",
 *         example="CN2023-0001",
 *         maxLength=255,
 *         nullable=true,
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Residential address of the student",
 *         example="123 Main St, Springfield",
 *         maxLength=255,
 *         nullable=true,
 *     ),
 *     @OA\Property(
 *         property="observation",
 *         type="string",
 *         description="Additional observations or notes regarding the student",
 *         example="Needs extra help in math",
 *         maxLength=255,
 *         nullable=true,
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         format="date",
 *         description="Birth date of the student",
 *         example="2005-08-15",
 *     ),
 * )
 */

class StudentStoreRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'profile_picture_path' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|boolean',
            'password' => [
                'required',
                'string',
                'min:6',            
                'regex:/[a-z]/',    
                'regex:/[A-Z]/',    
                'regex:/[0-9]/',    
                'regex:/[@$!%*#?&]/'
            ],
            'case_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'observation' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
        ];
    }
}
