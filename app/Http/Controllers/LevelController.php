<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LevelStoreRequest;
use App\Http\Requests\LevelUpdateRequest;
use App\Http\Resources\LevelResource;
use App\Repositories\LevelRepository;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Levels",
 *     description="API Endpoints for Level Management"
 * )
 */
class LevelController extends Controller
{
    protected $repository;

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
    public function index(Request $request)
    {
        try {
            $results = $this->repository->paginateWithFiltersAndSort(
                $request->query('filters', []),
                $request->query('search', ''),
                $request->query('per_page', 15),
                $request->query('sort_column', 'id'),
                $request->query('sort_direction', 'asc')
            );
            return ApiResponse::success($results);
            return ApiResponse::paginated($results, LevelResource::class);
        } catch (\Exception $e) {
            Log::error('Error fetching levels:', ['exception' => $e]);
            return ApiResponse::error('Error fetching levels.', 500);
        }
    }
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
    public function create(LevelStoreRequest $request)
    {
        try {
            $level = $this->repository->create($request->validated());
            return ApiResponse::success(LevelResource::make($level), 'Level created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating level: ', ['exception' => $e]);
            return ApiResponse::error('Error creating level: ' . $e->getMessage(), 500);
        }
    }
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
    public function read($id)
    {
        $level = $this->repository->find($id);
        if ($level) {
            return ApiResponse::success(LevelResource::make($level));
        }
        return ApiResponse::error('Level not found', 404);
    }
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
    public function update(LevelUpdateRequest $request, $id)
    {
        try {
            $success = $this->repository->update($id, $request->validated());
            if ($success) {
                return ApiResponse::success(LevelResource::make($success), 'Level updated successfully');
            }
            return ApiResponse::error('Level not found', 404);
        } catch (\Exception $e) {
            Log::error('Error updating level: ', ['exception' => $e]);
            return ApiResponse::error('Error updating level: ' . $e->getMessage(), 500);
        }
    }
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
    public function delete($id)
    {
        try {
            $success = $this->repository->delete($id);
            if ($success) {
                return ApiResponse::success(null, 'Level deleted successfully');
            }
            return ApiResponse::error('Level not found', 404);
        } catch (\Exception $e) {
            Log::error('Error deleting level: ', ['exception' => $e]);
            return ApiResponse::error('Error deleting level: ' . $e->getMessage(), 500);
        }
    }
}
