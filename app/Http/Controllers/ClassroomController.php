<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassroomResource;
use App\Repositories\ClassroomRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'capacity' => 'nullable|numeric|min:1'
    ];
    
    protected $updateValidationRules = [
        'name' => 'required|string|max:255',
        'capacity' => 'nullable|numeric|min:1'
    ];
    
    protected $uniqueFields = [];

    protected $resource = ClassroomResource::class;
    protected $resourceDetails = ClassroomResource::class;

    public function __construct(ClassroomRepository $repository)
    {
        $this->repository = $repository;
    }
}
