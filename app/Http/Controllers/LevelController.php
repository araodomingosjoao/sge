<?php

namespace App\Http\Controllers;

use App\Http\Resources\LevelResource;
use App\Repositories\LevelRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Levels",
 *     description="API Endpoints of Level Management"
 * )
 */
class LevelController extends Controller
{
    use CrudTrait;

    /**
     * @var array Validation rules for storing a new record
     * @OA\Schema(
     *     schema="LevelStoreRequest",
     *     description="Level creation request",
     *     required={"name","type_education_id","year"},
     *     @OA\Property(property="name", type="string", maxLength=255, description="Level name"),
     *     @OA\Property(property="type_education_id", type="string", description="Type of education ID"),
     *     @OA\Property(property="year", type="integer", minimum=1, description="Year of the level")
     * )
     */
    protected $storeValidationRules = [
        'name' => 'required|string|max:255',
        'type_education_id' => 'required|string|exists:type_educations,id',
        'year' => 'required|numeric|unique:levels,year|min:1',
    ];
    
    /**
     * @var array Validation rules for updating an existing record
     * @OA\Schema(
     *     schema="LevelUpdateRequest",
     *     description="Level update request",
     *     @OA\Property(property="name", type="string", maxLength=255, description="Level name"),
     *     @OA\Property(property="type_education_id", type="string", description="Type of education ID"),
     *     @OA\Property(property="year", type="integer", minimum=1, description="Year of the level")
     * )
     */
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
