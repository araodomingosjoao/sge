<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolResource;
use App\Repositories\SchoolRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'type_education_id' => 'required|string|exists:type_educations,id',
        'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'address' => 'nullable|string|max:255'
    ];
    
    protected $updateValidationRules = [
        'name' => 'nullable|string|max:255',
        'type_education_id' => 'nullable|string|exists:type_educations,id',
        'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'address' => 'nullable|string|max:255'
    ];
    

    protected $uniqueFields = [];

    protected $resource = SchoolResource::class;

    public function __construct(SchoolRepository $repository)
    {
        $this->repository = $repository;
    }
}
