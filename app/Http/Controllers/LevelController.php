<?php

namespace App\Http\Controllers;

use App\Http\Resources\LevelResource;
use App\Repositories\LevelRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'type_education_id' => 'required|string|exists:type_educations,id',
        'year' => 'required|numeric|unique:levels,year|min:1',
    ];
    
    protected $updateValidationRules = [
        'name' => 'sometimes|string|max:255',
        'type_education_id' => 'sometimes|string|exists:type_educations,id',
        'year' => 'sometimes|numeric|unique:levels,year|min:1',
    ];
    
    protected $uniqueFields = [
        'year' => 'levels'
    ];

    protected $resource = LevelResource::class;
    protected $resourceDetails = LevelResource::class;

    public function __construct(LevelRepository $repository)
    {
        $this->repository = $repository;
    }
}
