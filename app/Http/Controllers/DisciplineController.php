<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\DisciplineStoreRequest;
use App\Http\Requests\DisciplineUpdateRequest;
use App\Http\Resources\DisciplineResource;
use App\Repositories\DisciplineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Discipline",
 *     description="API Endpoints for Discipline"
 * )
 */
class DisciplineController extends Controller
{
    protected $repository;

    public function __construct(DisciplineRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list of Disciplines
     * 
     * @OA\Get(
     *     path="/discipline",
     *     tags={"Discipline"},
     *     summary="List Discipline with pagination",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering discipline",
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
     *         description="List of discipline",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DisciplineResource")
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

            return ApiResponse::paginated($results, DisciplineResource::class);
        } catch (\Exception $e) {
            Log::error('Error fetching Disciplines:', ['exception' => $e]);
            return ApiResponse::error('Error fetching Disciplines.', 500);
        }
    }

    /**
     * Create a new Discipline
     * 
     * @OA\Post(
     *     path="/discipline",
     *     tags={"Discipline"},
     *     summary="Create a new Discipline",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON object containing new Discipline data",
     *         @OA\JsonContent(ref="#/components/schemas/DisciplineStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discipline created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DisciplineResource")
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
    public function create(DisciplineStoreRequest $request)
    {
        try {
            $data = $this->repository->create($request->validated());
            return ApiResponse::success(DisciplineResource::make($data), 'Discipline created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating Discipline: ', ['exception' => $e]);
            return ApiResponse::error('Error creating Discipline: ' . $e->getMessage(), 500);
        }
    }
    /**
     * Show details of a specific Discipline
     *
     * @OA\Get(
     *     path="/discipline/{id}",
     *     tags={"Discipline"},
     *     summary="Retrieve details of a Discipline by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Discipline ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discipline data",
     *         @OA\JsonContent(ref="#/components/schemas/DisciplineResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discipline not found"
     *     )
     * )
     */
    public function read($id)
    {
        $data = $this->repository->find($id);
        if ($data) {
            return ApiResponse::success(DisciplineResource::make($data));
        }
        return ApiResponse::error('Discipline not found', 404);
    }
     /**
     * Update an existing Discipline
     * 
     * @OA\Put(
     *     path="/discipline/{id}",
     *     tags={"Discipline"},
     *     summary="Update an existing Discipline",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Discipline ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated Discipline data",
     *         @OA\JsonContent(ref="#/components/schemas/DisciplineUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discipline updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DisciplineResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discipline not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(DisciplineUpdateRequest $request, $id)
    {
        try {
            $success = $this->repository->update($id, $request->validated());
            if ($success) {
                return ApiResponse::success(DisciplineResource::make($success), 'Discipline updated successfully');
            }
            return ApiResponse::error('Discipline not found', 404);
        } catch (\Exception $e) {
            Log::error('Error updating Discipline: ', ['exception' => $e]);
            return ApiResponse::error('Error updating Discipline: ' . $e->getMessage(), 500);
        }
    }
     /**
     * Delete a specific Discipline
     * 
     * @OA\Delete(
     *     path="/discipline/{id}",
     *     tags={"Discipline"},
     *     summary="Delete a Discipline by ID",
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Discipline ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Discipline deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discipline not found"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $data = $this->repository->delete($id);
            if ($data) {
                return ApiResponse::success(null, 'Discipline deleted successfully');
            }
            return ApiResponse::error('Discipline not found', 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Discipline: ', ['exception' => $e]);
            return ApiResponse::error('Error deleting Discipline: ' . $e->getMessage(), 500);
        }
    }
}
