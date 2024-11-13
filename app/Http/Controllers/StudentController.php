<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Http\Resources\StudentResource;
use App\Models\Discipline;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use App\Repositories\StudentRepository;
use App\Services\FileUploadService;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Students",
 *     description="API Endpoints for Students"
 * )
 */
class StudentController extends Controller
{

    protected $repository;
    protected $fileUploadService;

    public function __construct(
        StudentRepository $repository,
        FileUploadService $fileUploadService
    ) {
        $this->repository = $repository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @OA\Get(
     *     path="/api/students/trashed",
     *     summary="Retrieve trashed students",
     *     tags={"Students"},
     *     @OA\Response(
     *         response=200,
     *         description="List of trashed students retrieved successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function trashed()
    {
        $students = Student::onlyTrashed()->get();

        return ApiResponse::success($students, 'Trashed students retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/students/restore/{id}",
     *     summary="Restore a trashed student",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the trashed student to restore",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student restored successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        return ApiResponse::success($student, 'Trashed students retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="Create a new student",
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating student"
     *     )
     * )
     */

    public function create(StudentStoreRequest $request)
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
            $student = Student::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'case_number' => $request->case_number,
                'observation' => $request->observation,
            ]);

            $role = Role::findOrCreate('student', 'web');
            $user->assignRole($role);

            DB::commit();

            return ApiResponse::success(StudentResource::make($student), 'Student created successfully');
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return ApiResponse::error('Error creating student: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/students/{id}",
     *     summary="Update an existing student",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the student to update",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error updating student"
     *     )
     * )
     */
    public function update(StudentStoreRequest $request, string $id)
    {
        DB::beginTransaction();

        try {
            $student = Student::find($id);
            $user = $student->user;
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

            $student->update([
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'case_number' => $request->case_number,
                'observation' => $request->observation,
            ]);

            $role = Role::findOrCreate('student', 'web');
            $user->syncRoles([$role]);

            DB::commit();

            return ApiResponse::success(StudentResource::make($student), 'Student updated successfully');
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return ApiResponse::error('Error updating student: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/students/enroll",
     *     summary="Enroll a student in a class",
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EnrollmentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student enrolled successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Student already enrolled"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error enrolling student"
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="EnrollmentRequest",
     *     type="object",
     *     required={"student_id", "class_id", "academic_year"},
     *     @OA\Property(property="student_id", type="string"),
     *     @OA\Property(property="class_id", type="string"),
     *     @OA\Property(property="academic_year", type="string")
     * )
     */
    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            // Verifica se o aluno já está matriculado no ano letivo
            $existingEnrollment = StudentClass::where('student_id', $validated['student_id'])
                ->where('academic_year', $validated['academic_year'])
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'message' => 'Student is already enrolled for this academic year.',
                ], 400);
            }

            $student = StudentClass::create([
                'student_id' => $validated['student_id'],
                'class_id' => $validated['class_id'],
                'academic_year' => $validated['academic_year'],
                'status' => 'active'
            ]);

            DB::commit();

            return ApiResponse::success($student, 'Student successfully enrolled.', 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return ApiResponse::error('Error occurred during enrollment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/students/{student_id}/enrollments/{enrollment_id}/status",
     *     summary="Update academic status of a student's enrollment",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="ID of the student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="enrollment_id",
     *         in="path",
     *         required=true,
     *         description="ID of the enrollment",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EnrollmentStatusRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Academic status updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Enrollment not found"
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="EnrollmentStatusRequest",
     *     type="object",
     *     required={"status_academic"},
     *     @OA\Property(property="status_academic", type="string", example="promoted, repeated, transferred")
     * )
     */
    public function updateEnrollmentStatus($student_id, $enrollment_id, Request $request)
    {
        $validated = $request->validate([
            'status_academic' => 'required|in:promoted,repeated,transferred',
        ]);

        $enrollment = StudentClass::where('id', $enrollment_id)
            ->where('student_id', $student_id)
            ->firstOrFail();

        $enrollment->status_academic = $validated['status_academic'];
        $enrollment->save();

        return response()->json(['message' => 'Academic status updated successfully.']);
    }

    /**
     * @OA\Post(
     *     path="/api/students/progress",
     *     summary="Handle student progress",
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StudentProgressRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student progress updated successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error updating student progress"
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="StudentProgressRequest",
     *     type="object",
     *     required={"student_id", "academic_year"},
     *     @OA\Property(property="student_id", type="string"),
     *     @OA\Property(property="next_class_id", type="string"),
     *     @OA\Property(property="academic_year", type="string"),
     *     @OA\Property(property="progress_status", type="string", example="promoted, repeated, transferred"),
     * )
     */
    public function handleStudentProgress(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'next_class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'required|integer',
            'progress_status' => 'required|in:promoted,repeated,transferred',
        ]);

        DB::table('student_class')
            ->where('student_id', $validated['student_id'])
            ->where('status', 'active')
            ->update([
                'status' => 'completed',
                'status_academic' => $validated['progress_status']
            ]);

        // Se o aluno for promovido, cria um novo registro na nova turma
        if ($validated['progress_status'] == 'promoted') {
            DB::table('student_class')->insert([
                'id' => (string) Str::uuid(),
                'student_id' => $validated['student_id'],
                'class_id' => $validated['next_class_id'],
                'academic_year' => $validated['academic_year'],
                'status' => 'active',
                'status_academic' => 'promoted',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Student progress updated successfully.']);
    }

    /**
     * @OA\Get(
     *     path="/api/students/{student_id}/enrollments",
     *     summary="Retrieve student's enrollment history",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="ID of the student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student enrollments retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items()
     *         )
     *     )
     * )
     */
    public function getStudentEnrollments($student_id)
    {
        $enrollments = StudentClass::where('student_id', $student_id)
            ->with('class.level.typeEducation')
            ->orderBy('academic_year', 'desc')
            ->get();

        return response()->json($enrollments);
    }

    /**
     * @OA\Post(
     *     path="/api/students/{student_id}/transfer",
     *     summary="Transfer a student to a new class",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="ID of the student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TransferRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student transferred successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */

    /**
     * @OA\Schema(
     *     schema="TransferRequest",
     *     type="object",
     *     required={"new_class_id", "current_academic_year"},
     *     @OA\Property(property="new_class_id", type="string"),
     *     @OA\Property(property="current_academic_year", type="integer"),
     * )
     */
    public function transferStudent(Request $request, $student_id)
    {
        $validated = $request->validate([
            'new_class_id' => 'required|exists:classes,id',
            'current_academic_year' => 'required|integer',
        ]);

        // Atualiza a matrícula atual para "transferred"
        DB::table('student_class')
            ->where('student_id', $student_id)
            ->where('academic_year', $validated['current_academic_year'])
            ->update([
                'status_academic' => 'transferred',
                'status' => 'completed'
            ]);

        // Cria nova matrícula na nova turma
        StudentClass::create([
            'id' => (string) Str::uuid(),
            'student_id' => $student_id,
            'class_id' => $validated['new_class_id'],
            'academic_year' => $validated['current_academic_year'],
            'status' => 'active',
            'status_academic' => 'transferred'
        ]);

        return response()->json(['message' => 'Student transferred successfully.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{student_id}/enrollments/{enrollment_id}",
     *     summary="Cancel an enrollment",
     *     tags={"Students"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="ID of the student",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="enrollment_id",
     *         in="path",
     *         required=true,
     *         description="ID of the enrollment to cancel",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrollment canceled successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Enrollment not found"
     *     )
     * )
     */
    public function cancelEnrollment($student_id, $enrollment_id)
    {
        $enrollment = StudentClass::where('id', $enrollment_id)
            ->where('student_id', $student_id)
            ->firstOrFail();

        $enrollment->status = 'canceled';
        $enrollment->save();

        return response()->json(['message' => 'Enrollment canceled successfully.']);
    }
}
