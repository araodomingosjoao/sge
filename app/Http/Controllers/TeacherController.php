<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\TeacherStoreRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Http\Resources\TeacherResource;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use App\Repositories\TeacherRepository;
use App\Services\FileUploadService;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Teacher",
 *     description="API Endpoints for Teacher"
 * )
 */
class TeacherController extends Controller
{
    protected $repository;
    protected $fileUploadService;

    public function __construct(
        TeacherRepository $repository,
        FileUploadService $fileUploadService
    )
    {
        $this->repository = $repository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new teacher
     * 
     * @OA\Post(
     *     path="/teacher",
     *     tags={"Teacher"},
     *     summary="Create a new teacher",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new teacher data",
     *         @OA\JsonContent(ref="#/components/schemas/TeacherStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function create(TeacherStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $requestData = $request->validated();
            $requestData['password'] = Hash::make($requestData['password']);
    
            if ($request->hasFile('profile_picture_path')) {
                $requestData['profile_picture_path'] = $this->fileUploadService->handleSingleFile(
                    $request->file('profile_picture_path'), 
                    User::getFileFields()['profile_picture_path']
                );
            }
    
            $user = User::create($requestData);
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
            ]);
            
            $role = Role::findOrCreate('teacher', 'web');
            $user->assignRole($role);

            DB::commit();

            return ApiResponse::success(TeacherResource::make($teacher), 'Teacher created successfully');
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return ApiResponse::error('Error creating teacher: ' . $e->getMessage(), 500);
        }
    }

    public function update(TeacherUpdateRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::find($id);
            $user = $teacher->user;
            $requestData = $request->validationData();

            if (isset($requestData['password'])) {
                $requestData['password'] = Hash::make($requestData['password']);
            }

            if ($request->hasFile('profile_picture_path')) {
                $this->fileUploadService->deleteOldFile(
                    $user->profile_picture_path,
                    User::getFileFields()['profile_picture_path']['disk']
                );
                $requestData['profile_picture_path'] = $this->fileUploadService->handleSingleFile(
                    $request->file('profile_picture_path'), 
                    User::getFileFields()['profile_picture_path']
                );
            }

            $user->update($requestData);

            $teacher->update([
                'address' => $request->address,
                'birth_date' => $request->birth_date,
            ]);

            $role = Role::findOrCreate('teacher', 'web');
            $user->syncRoles([$role]);

            DB::commit();

            return ApiResponse::success(TeacherResource::make($teacher), 'Teacher updated successfully');
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return ApiResponse::error('Error updating teacher: ' . $e->getMessage(), 500);
        }
    }

}
