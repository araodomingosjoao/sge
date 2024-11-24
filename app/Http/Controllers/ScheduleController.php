<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ScheduleStoreRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Schedule",
 *     description="API Endpoints for Schedule"
 * )
 */
class ScheduleController extends Controller
{
    protected $repository;

    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list of Schedules
     * 
     * @OA\Get(
     *     path="/schedule",
     *     tags={"Schedule"},
     *     summary="List Schedule with pagination",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering schedule",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="sort_column",
     *         in="query",
     *         description="Column to sort by",
     *         required=false,
     *         @OA\Schema(type="string", default="id")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Direction for sorting (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc","desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedule",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ScheduleResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $results = $this->repository->paginateWithFiltersAndSort(
                $request->query('filters', []),
                $request->query('search', ''),
                $request->query('per_page', 15),
                $request->query('sort_column', 'id'),
                $request->query('sort_direction', 'asc')
            );

            return ApiResponse::paginated($results, ScheduleResource::class);
        } catch (\Exception $e) {
            Log::error('Error fetching Schedules:', ['exception' => $e]);
            return ApiResponse::error('Error fetching Schedules.', 500);
        }
    }

    /**
     * Create a new Schedule
     * 
     * @OA\Post(
     *     path="/schedule",
     *     tags={"Schedule"},
     *     summary="Create a new Schedule",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new Schedule data",
     *         @OA\JsonContent(ref="#/components/schemas/ScheduleStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Schedule created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ScheduleResource")
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
    public function create(ScheduleStoreRequest $request)
    {
        $validatedData = $request->validated();

        // Verificar se o professor está disponível
        $conflictingScheduleTeacher = Schedule::where('teacher_id', $validatedData['teacher_id'])
            ->where('day', $validatedData['day'])
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
                    ->orWhereBetween('end_time', [$validatedData['start_time'], $validatedData['end_time']]);
            })
            ->exists();

        if ($conflictingScheduleTeacher) {
            return response()->json([
                'message' => 'Teacher is not available during this time.'
            ], 409);
        }

        // Verificar se a sala de aula está disponível
        $conflictingScheduleClassroom = Schedule::where('classroom_id', $validatedData['classroom_id'])
            ->where('day', $validatedData['day'])
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
                    ->orWhereBetween('end_time', [$validatedData['start_time'], $validatedData['end_time']]);
            })
            ->exists();

        if ($conflictingScheduleClassroom) {
            return response()->json([
                'message' => 'Classroom is not available during this time.'
            ], 409);
        }

        $schedule = Schedule::create($validatedData);

        return ApiResponse::success(ScheduleResource::make($schedule), 'Schedule created successfully.', 201);
    }
    /**
     * Show details of a specific Schedule
     *
     * @OA\Get(
     *     path="/schedule/{id}",
     *     tags={"Schedule"},
     *     summary="Retrieve details of a Schedule by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Schedule ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule data",
     *         @OA\JsonContent(ref="#/components/schemas/ScheduleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
     */
    public function read($id)
    {
        $data = $this->repository->find($id);
        if ($data) {
            return ApiResponse::success(ScheduleResource::make($data));
        }
        return ApiResponse::error('Schedule not found', 404);
    }
     /**
     * Update an existing Schedule
     * 
     * @OA\Put(
     *     path="/schedule/{id}",
     *     tags={"Schedule"},
     *     summary="Update an existing Schedule",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Schedule ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated Schedule data",
     *         @OA\JsonContent(ref="#/components/schemas/ScheduleUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ScheduleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(ScheduleUpdateRequest $request, $id)
    {
        try {
            $success = $this->repository->update($id, $request->validated());
            if ($success) {
                return ApiResponse::success(ScheduleResource::make($success), 'Schedule updated successfully');
            }
            return ApiResponse::error('Schedule not found', 404);
        } catch (\Exception $e) {
            Log::error('Error updating Schedule: ', ['exception' => $e]);
            return ApiResponse::error('Error updating Schedule: ' . $e->getMessage(), 500);
        }
    }
     /**
     * Delete a specific Schedule
     * 
     * @OA\Delete(
     *     path="/schedule/{id}",
     *     tags={"Schedule"},
     *     summary="Delete a Schedule by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Schedule ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Schedule deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $data = $this->repository->delete($id);
            if ($data) {
                return ApiResponse::success(null, 'Schedule deleted successfully');
            }
            return ApiResponse::error('Schedule not found', 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Schedule: ', ['exception' => $e]);
            return ApiResponse::error('Error deleting Schedule: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/schedule/class/{class_id}",
     *     summary="Get schedules by class ID",
     *     description="Retrieve a list of schedules filtered by class ID",
     *     tags={"Schedule"},
     *     @OA\Parameter(
     *         name="class_id",
     *         in="path",
     *         required=true,
     *         description="ID of the class",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules for the class",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ScheduleResource"))
     *     )
     * )
     */
    public function getSchedulesByClass($class_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                        ->where('class_id', $class_id)
                        ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

    /**
     * @OA\Get(
     *     path="/api/schedule/teacher/{teacher_id}",
     *     summary="Get schedules by teacher ID",
     *     description="Retrieve a list of schedules filtered by teacher ID",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="teacher_id",
     *         in="path",
     *         required=true,
     *         description="ID of the teacher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules for the teacher",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ScheduleResource"))
     *     )
     * )
     */
    public function getSchedulesByTeacher($teacher_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                        ->where('teacher_id', $teacher_id)
                        ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

    /**
     * @OA\Get(
     *     path="/api/schedule/discipline/{discipline_id}",
     *     summary="Get schedules by discipline ID",
     *     description="Retrieve a list of schedules filtered by discipline ID",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="discipline_id",
     *         in="path",
     *         required=true,
     *         description="ID of the discipline",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules for the discipline",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ScheduleResource"))
     *     )
     * )
     */
    public function getSchedulesByDiscipline($discipline_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                            ->where('discipline_id', $discipline_id)
                            ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

    /**
     * @OA\Get(
     *     path="/api/schedule/teacher/{teacher_id}/availability",
     *     summary="Check teacher availability",
     *     description="Check if a teacher is available on a given day and time",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="teacher_id",
     *         in="path",
     *         required=true,
     *         description="ID of the teacher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="day",
     *         in="query",
     *         required=true,
     *         description="Day of the week",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         required=true,
     *         description="Start time of the schedule",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         required=true,
     *         description="End time of the schedule",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Teacher is available",
     *         @OA\JsonContent(@OA\Schema(type="string", example="Teacher is available"))
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Teacher is not available",
     *         @OA\JsonContent(@OA\Schema(type="string", example="Teacher is not available during this time"))
     *     )
     * )
     */
    public function checkTeacherAvailability(Request $request, $teacher_id)
    {
        $day = $request->query('day');
        $startTime = $request->query('start_time');
        $endTime = $request->query('end_time');

        $conflictingSchedule = Schedule::where('teacher_id', $teacher_id)
                                    ->where('day', $day)
                                    ->where(function($query) use ($startTime, $endTime) {
                                        $query->whereBetween('start_time', [$startTime, $endTime])
                                                ->orWhereBetween('end_time', [$startTime, $endTime]);
                                    })->exists();

        if ($conflictingSchedule) {
            return response()->json(['message' => 'Teacher is not available during this time.'], 409);
        }

        return response()->json(['message' => 'Teacher is available.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/schedule/day/{day}",
     *     summary="Get schedules by day",
     *     description="Retrieve a list of schedules filtered by a specific day",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="day",
     *         in="path",
     *         required=true,
     *         description="Day of the week",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules for the day",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ScheduleResource"))
     *     )
     * )
     */
    public function getSchedulesByDay($day)
    {
        $schedules = Schedule::with(['teacher', 'class', 'discipline', 'classroom'])
                            ->where('day', $day)
                            ->get();

        return response()->json($schedules);
    }

    /**
     * @OA\Get(
     *     path="/schedule/classroom/availability",
     *     summary="Check classroom availability",
     *     description="Check if a classroom is available on a given day and time",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="day",
     *         in="query",
     *         required=true,
     *         description="Day of the week",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         required=true,
     *         description="Start time of the schedule",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         required=true,
     *         description="End time of the schedule",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classroom is available",
     *         @OA\JsonContent(@OA\Schema(type="string", example="Classroom is available"))
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Classroom is not available",
     *         @OA\JsonContent(@OA\Schema(type="string", example="Classroom is not available during this time"))
     *     )
     * )
     */
    public function checkClassroomAvailability(Request $request)
    {
        $day = $request->query('day');
        $startTime = $request->query('start_time');
        $endTime = $request->query('end_time');

        $conflictingSchedule = Schedule::where('day', $day)
                                    ->where(function($query) use ($startTime, $endTime) {
                                        $query->whereBetween('start_time', [$startTime, $endTime])
                                                ->orWhereBetween('end_time', [$startTime, $endTime]);
                                    })->exists();

        if ($conflictingSchedule) {
            return response()->json(['message' => 'Classroom is not available during this time.'], 409);
        }

        return response()->json(['message' => 'Classroom is available.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/schedule/class/{class_id}/weekly",
     *     summary="Get weekly schedule by class ID",
     *     description="Retrieve the weekly schedule for a specific class",
     *     tags={"Schedule"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="class_id",
     *         in="path",
     *         required=true,
     *         description="ID of the class",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Weekly schedule grouped by day",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ScheduleResource"))
     *     )
     * )
     */
    public function getWeeklyScheduleByClass($class_id)
    {
        $schedules = Schedule::with(['teacher', 'discipline', 'classroom', 'class'])
                             ->where('class_id', $class_id)
                             ->orderBy('day')
                             ->orderBy('start_time')
                             ->get();
    
        $weeklySchedule = $schedules->groupBy('day');
    
        return response()->json($weeklySchedule);
    }
    
}
