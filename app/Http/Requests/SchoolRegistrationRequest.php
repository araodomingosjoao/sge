<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="SchoolRegistrationRequest",
 *     required={"type_education_id", "school_name", "email", "first_name", "last_name", "password", "phone", "address"},
 *     @OA\Property(property="type_education_id", type="integer", example=1, description="ID for the type of education"),
 *     @OA\Property(property="school_name", type="string", maxLength=255, example="Bright Future School", description="Name of the school"),
 *     @OA\Property(property="email", type="string", format="email", example="school@example.com", description="Email for the school"),
 *     @OA\Property(property="first_name", type="string", maxLength=255, example="John", description="First name of the school admin"),
 *     @OA\Property(property="last_name", type="string", maxLength=255, example="Doe", description="Last name of the school admin"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, example="strongPassword123", description="Password for the school admin account"),
 *     @OA\Property(property="phone", type="string", maxLength=20, example="+123456789", description="Phone number for the school admin"),
 *     @OA\Property(property="address", type="string", maxLength=255, example="123 Main St", description="School address"),
 *     @OA\Property(property="city", type="string", maxLength=100, example="Metropolis", description="City of the school"),
 *     @OA\Property(property="state", type="string", maxLength=100, example="StateName", description="State of the school"),
 *     @OA\Property(property="country", type="string", maxLength=100, example="CountryName", description="Country of the school"),
 *     @OA\Property(property="postal_code", type="string", maxLength=20, example="12345", description="Postal code of the school"),
 *     @OA\Property(property="website", type="string", format="url", maxLength=255, example="http://schoolwebsite.com", description="School's website URL"),
 *     @OA\Property(property="founded_year", type="integer", minimum=1800, example=1999, description="Year the school was founded"),
 *     @OA\Property(property="registration_number", type="string", maxLength=100, example="REG123456", description="Registration number of the school"),
 * )
 */
class SchoolRegistrationRequest extends FormRequest
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
            'type_education_id' => 'required|numeric|exists:type_educations,id',
            'school_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|unique:users,phone|max:20',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'founded_year' => 'nullable|numeric|min:1800',
            'registration_number' => 'nullable|string|max:100',
        ];
    }
}
