<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\SchoolRegistrationRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SchoolRegistrationConfirmation;
use App\Models\Role;

class SchoolRegistrationController extends Controller
{
    public function register(SchoolRegistrationRequest $request)
    {
        DB::beginTransaction();

        try {
            $school = School::create($request->validationData());

            $user = User::create([
                'school_id' => $school->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            $role = Role::findOrCreate('school_admin');
            $user->assignRole($role);

            DB::commit();

            // Mail::to($user->email)->send(new SchoolRegistrationConfirmation($school, $user));
            return ApiResponse::success([], 'School and admin user registered successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed. Please try again.', $e->getMessage()], 500);
        }
    }
}