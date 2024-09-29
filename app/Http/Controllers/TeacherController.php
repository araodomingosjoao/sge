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

class TeacherController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [];
    protected $updateValidationRules = [];
    
    protected $uniqueFields = [];

    protected $resource = TeacherResource::class;
    protected $resourceDetails = TeacherResource::class;
    protected $fileUploadService;

    public function __construct(
        TeacherRepository $repository,
        FileUploadService $fileUploadService
    )
    {
        $this->repository = $repository;
        $this->fileUploadService = $fileUploadService;
    }

    public function create(TeacherStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $requestData = $request->validationData();
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
