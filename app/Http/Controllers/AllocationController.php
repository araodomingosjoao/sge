<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllocationResource;
use App\Http\Resources\TeacherResource;
use App\Models\TeacherDisciplineClass;
use App\Repositories\AllocationRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class AllocationController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'teacher_id' => 'required|exists:teachers,id',
        'discipline_id' => 'required|exists:disciplines,id',
        'class_id' => 'required|exists:classes,id',
    ];
    
    protected $updateValidationRules = [
        'teacher_id' => 'required|exists:teachers,id',
        'discipline_id' => 'required|exists:disciplines,id',
        'class_id' => 'required|exists:classes,id',
    ];
    
    protected $uniqueFields = [];

    protected $resource = AllocationResource::class;
    protected $resourceDetails = AllocationResource::class;

    public function __construct(AllocationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTeachersByDiscipline($discipline_id)
    {
        $teachers = TeacherDisciplineClass::with('teacher')
                    ->where('discipline_id', $discipline_id)
                    ->get()
                    ->pluck('teacher');

        return response()->json(TeacherResource::collection($teachers));
    }

    public function getTeachersByClass($class_id)
    {
        $teachers = TeacherDisciplineClass::with('teacher')
                    ->where('class_id', $class_id)
                    ->get()
                    ->pluck('teacher');

        return response()->json(TeacherResource::collection($teachers));
    }

}
