<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ClassStoreRequest;
use App\Http\Requests\ClassUpdateRequest;
use App\Http\Resources\ClassResource;
use App\Repositories\ClassRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Class",
 *     description="API Endpoints for Class"
 * )
 */
class ClassController extends Controller
{
    protected $repository;

    public function __construct(ClassRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list of classes
     * 
     * @OA\Get(
     *     path="/class",
     *     tags={"Class"},
     *     summary="List classs with pagination",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering classs",
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
     *         description="List of classs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClassResource")
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

            return ApiResponse::paginated($results, ClassResource::class);
        } catch (\Exception $e) {
            Log::error('Error fetching classes:', ['exception' => $e]);
            return ApiResponse::error('Error fetching classes.', 500);
        }
    }

    /**
     * Create a new class
     * 
     * @OA\Post(
     *     path="/class",
     *     tags={"Class"},
     *     summary="Create a new class",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new class data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassResource")
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
    public function create(ClassStoreRequest $request)
    {
        try {
            $class = $this->repository->create($request->validated());
            return ApiResponse::success(ClassResource::make($class), 'Class created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating class: ', ['exception' => $e]);
            return ApiResponse::error('Error creating class: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Show details of a specific class
     *
     * @OA\Get(
     *     path="/class/{id}",
     *     tags={"Class"},
     *     summary="Retrieve details of a class by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Class ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found"
     *     )
     * )
     */
    public function read($id)
    {
        $class = $this->repository->find($id);
        if ($class) {
            return ApiResponse::success(ClassResource::make($class));
        }
        return ApiResponse::error('Class not found', 404);
    }
     /**
     * Update an existing class
     * 
     * @OA\Put(
     *     path="/class/{id}",
     *     tags={"Class"},
     *     summary="Update an existing class",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Class ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated class data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Class updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(ClassUpdateRequest $request, $id)
    {
        try {
            $success = $this->repository->update($id, $request->validated());
            if ($success) {
                return ApiResponse::success(ClassResource::make($success), 'Class updated successfully');
            }
            return ApiResponse::error('Class not found', 404);
        } catch (\Exception $e) {
            Log::error('Error updating class: ', ['exception' => $e]);
            return ApiResponse::error('Error updating class: ' . $e->getMessage(), 500);
        }
    }
     /**
     * Delete a specific class
     * 
     * @OA\Delete(
     *     path="/class/{id}",
     *     tags={"Class"},
     *     summary="Delete a class by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Class ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Class deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Class not found"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $success = $this->repository->delete($id);
            if ($success) {
                return ApiResponse::success(null, 'Class deleted successfully');
            }
            return ApiResponse::error('Class not found', 404);
        } catch (\Exception $e) {
            Log::error('Error deleting class: ', ['exception' => $e]);
            return ApiResponse::error('Error deleting class: ' . $e->getMessage(), 500);
        }
    }
}
