<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\SetupService;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API Endpoints for Authentication"
 * )
 */
class AuthController extends Controller
{
    protected $setupService;

    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }
    /**
     * Login a user and generate a token
     * 
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Login a user and receive a Bearer token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your_access_token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || in_array($user->status, [
            UserStatus::INACTIVE,
            UserStatus::BLOCKED,
            UserStatus::SUSPENDED,
            UserStatus::PENDENT
        ])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout a user and revoke the current token
     * 
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="User logout",
     *     description="Logout the current user and invalidate the token",
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get the profile of the logged-in user
     * 
     * @OA\Get(
     *     path="/user/profile",
     *     tags={"Auth"},
     *     summary="Get user profile",
     *     description="Retrieve the profile information of the logged-in user",
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="role", type="string", example="Admin"),
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="string", example="edit_posts")
     *             )
     *         )
     *     )
     * )
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;
        $role = $user->getRoleNames()->first();
        $permissions = $user->getAllPermissions()->pluck('name');

        return response()->json([
            'setup_required' => !$this->setupService->isSetupComplete($schoolId),
            'user' => UserResource::make($user),
            'role' => $role,
            'permissions' => $permissions
        ]);
    }
}
