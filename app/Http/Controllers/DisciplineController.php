<?php

namespace App\Http\Controllers;

use App\Http\Resources\DisciplineResource;
use App\Repositories\DisciplineRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class DisciplineController extends Controller
{
    use CrudTrait;

    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
    ];
    
    protected $updateValidationRules = [
        'name' => 'sometimes|string|max:255',
    ];
    
    protected $uniqueFields = [];

    protected $resource = DisciplineResource::class;
    protected $resourceDetails = DisciplineResource::class;

    public function __construct(DisciplineRepository $repository)
    {
        $this->repository = $repository;
    }
}
