<?php

namespace App\Http\Controllers;

use App\Http\Resources\School\SchoolDetailResource;
use App\Http\Resources\School\SchoolResource;
use App\Repositories\SchoolRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'type_education_id' => 'required|string|exists:type_educations,id',
        'logo_path' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:255',
        'website' => 'nullable|string|max:255',
        'founded_year' => 'nullable|string|max:255',
        'registration_number' => 'nullable|string|max:255',
    ];
    
    protected $updateValidationRules = [
        'name' => 'nullable|string|max:255',
        'type_education_id' => 'nullable|string|exists:type_educations,id',
        'logo_path' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:255',
        'website' => 'nullable|string|max:255',
        'founded_year' => 'nullable|string|max:255',
        'registration_number' => 'nullable|string|max:255',
    ];
    

    protected $uniqueFields = [];

    protected $resource = SchoolResource::class;
    protected $resourceDetails = SchoolDetailResource::class;

    public function __construct(SchoolRepository $repository)
    {
        $this->repository = $repository;
    }
}
