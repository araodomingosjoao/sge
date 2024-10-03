<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassResource;
use App\Repositories\ClassRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'course_id' => 'nullable|string|exists:courses,id',
        'level_id' => 'required|string|exists:levels,id',
    ];
    
    protected $updateValidationRules = [
        'name' => 'sometimes|string|max:255',
        'course_id' => 'nullable|string|exists:courses,id',
        'level_id' => 'sometimes|string|exists:levels,id',
    ];
    
    protected $uniqueFields = [];

    protected $resource = ClassResource::class;
    protected $resourceDetails = ClassResource::class;

    public function __construct(ClassRepository $repository)
    {
        $this->repository = $repository;
    }
}
