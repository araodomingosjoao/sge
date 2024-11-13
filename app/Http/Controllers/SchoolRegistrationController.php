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
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="School Registration",
 *     description="Endpoints for registering a school and admin user"
 * )
 */
class SchoolRegistrationController extends Controller
{
    /**
     * Register a new school along with an admin user.
     * 
     * @OA\Post(
     *     path="/school/register",
     *     tags={"Auth"},
     *     summary="Register a new school and its admin user",
     *     description="Creates a new school along with an admin user assigned to that school.",
     *     operationId="registerSchool",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SchoolRegistrationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="School and admin user registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="School and admin user registered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Registration failed. Please try again.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Registration failed. Please try again.")
     *         )
     *     )
     * )
     */
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

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e); 
            return response()->json(['error' => 'Registration failed. Please try again.'], 500);
        }
    }
}
