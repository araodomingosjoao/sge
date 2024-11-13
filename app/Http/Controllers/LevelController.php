<?php

namespace App\Http\Controllers;

use App\Http\Resources\LevelResource;
use App\Repositories\LevelRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Levels",
 *     description="API Endpoints for Level Management"
 * )
 */
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
    
    /**
     * Get paginated list of levels
     * 
     * @OA\Get(
     *     path="/level",
     *     tags={"Levels"},
     *     summary="List levels with pagination",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering levels",
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
     *         description="List of levels",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Level")
     *         )
     *     )
     * )
     */
    
    /**
     * Create a new level
     * 
     * @OA\Post(
     *     path="/level",
     *     tags={"Levels"},
     *     summary="Create a new level",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new level data",
     *         @OA\JsonContent(ref="#/components/schemas/Level")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Level created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Level")
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
    
    /**
     * Show details of a specific level
     *
     * @OA\Get(
     *     path="/level/{id}",
     *     tags={"Levels"},
     *     summary="Retrieve details of a level by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Level ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Level data",
     *         @OA\JsonContent(ref="#/components/schemas/Level")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Level not found"
     *     )
     * )
     */
    
     /**
     * Update an existing level
     * 
     * @OA\Put(
     *     path="/level/{id}",
     *     tags={"Levels"},
     *     summary="Update an existing level",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Level ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated level data",
     *         @OA\JsonContent(ref="#/components/schemas/Level")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Level updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Level")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Level not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    
     /**
     * Delete a specific level
     * 
     * @OA\Delete(
     *     path="/level/{id}",
     *     tags={"Levels"},
     *     summary="Delete a level by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Level ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Level deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Level not found"
     *     )
     * )
     */
}
