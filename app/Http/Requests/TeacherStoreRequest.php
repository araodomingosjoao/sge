<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherStoreRequest extends FormRequest
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
        ];
    }
}
