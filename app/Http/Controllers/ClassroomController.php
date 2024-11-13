<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ClassroomStoreRequest;
use App\Http\Requests\ClassroomUpdateRequest;
use App\Http\Resources\ClassroomResource;
use App\Repositories\ClassroomRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Classroom",
 *     description="API Endpoints for Classroom"
 * )
 */
class ClassroomController extends Controller
{
    protected $repository;

    public function __construct(ClassroomRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list of classrooms
     * 
     * @OA\Get(
     *     path="/classroom",
     *     tags={"Classroom"},
     *     summary="List classroom with pagination",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering classroom",
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
     *         description="List of classroom",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClassroomResource")
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

            return ApiResponse::paginated($results, ClassroomResource::class);
        } catch (\Exception $e) {
            Log::error('Error fetching classrooms:', ['exception' => $e]);
            return ApiResponse::error('Error fetching classrooms.', 500);
        }
    }

    /**
     * Create a new classroom
     * 
     * @OA\Post(
     *     path="/classroom",
     *     tags={"Classroom"},
     *     summary="Create a new classroom",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new classroom data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassroomStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Classroom created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassroomResource")
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
    public function create(ClassroomStoreRequest $request)
    {
        try {
            $data = $this->repository->create($request->validated());
            return ApiResponse::success(ClassroomResource::make($data), 'Classroom created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating classroom: ', ['exception' => $e]);
            return ApiResponse::error('Error creating classroom: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Show details of a specific classroom
     *
     * @OA\Get(
     *     path="/classroom/{id}",
     *     tags={"Classroom"},
     *     summary="Retrieve details of a classroom by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classroom data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassroomResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Classroom not found"
     *     )
     * )
     */
    public function read($id)
    {
        $data = $this->repository->find($id);
        if ($data) {
            return ApiResponse::success(ClassroomResource::make($data));
        }
        return ApiResponse::error('Classroom not found', 404);
    }
     /**
     * Update an existing classroom
     * 
     * @OA\Put(
     *     path="/classroom/{id}",
     *     tags={"Classroom"},
     *     summary="Update an existing classroom",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated classroom data",
     *         @OA\JsonContent(ref="#/components/schemas/ClassroomUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classroom updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ClassroomResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Classroom not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(ClassroomUpdateRequest $request, $id)
    {
        try {
            $success = $this->repository->update($id, $request->validated());
            if ($success) {
                return ApiResponse::success(ClassroomResource::make($success), 'Classroom updated successfully');
            }
            return ApiResponse::error('Classroom not found', 404);
        } catch (\Exception $e) {
            Log::error('Error updating classroom: ', ['exception' => $e]);
            return ApiResponse::error('Error updating classroom: ' . $e->getMessage(), 500);
        }
    }
     /**
     * Delete a specific classroom
     * 
     * @OA\Delete(
     *     path="/classroom/{id}",
     *     tags={"Classroom"},
     *     summary="Delete a classroom by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Classroom ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Classroom deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Classroom not found"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $data = $this->repository->delete($id);
            if ($data) {
                return ApiResponse::success(null, 'Classroom deleted successfully');
            }
            return ApiResponse::error('Classroom not found', 404);
        } catch (\Exception $e) {
            Log::error('Error deleting classroom: ', ['exception' => $e]);
            return ApiResponse::error('Error deleting classroom: ' . $e->getMessage(), 500);
        }
    }
}
