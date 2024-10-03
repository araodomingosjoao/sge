<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [];
    protected $updateValidationRules = [];
    
    protected $uniqueFields = [];

    protected $resource = ScheduleResource::class;
    protected $resourceDetails = ScheduleResource::class;

    public function __construct(ScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'teacher_id'    => 'required|exists:teachers,id',
            'class_id'      => 'required|exists:classes,id',
            'discipline_id' => 'required|exists:disciplines,id',
            'classroom_id'  => 'required|exists:classrooms,id',
            'day'           => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ]);

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

    public function getSchedulesByClass($class_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                        ->where('class_id', $class_id)
                        ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

    public function getSchedulesByTeacher($teacher_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                        ->where('teacher_id', $teacher_id)
                        ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

    public function getSchedulesByDiscipline($discipline_id)
    {
        $schedules = Schedule::with(['discipline', 'teacher', 'classroom', 'class'])
                            ->where('discipline_id', $discipline_id)
                            ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

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

    public function getSchedulesByDay($day)
    {
        $schedules = Schedule::with(['teacher', 'class', 'discipline', 'classroom'])
                            ->where('day', $day)
                            ->get();

        return response()->json(ScheduleResource::collection($schedules));
    }

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
