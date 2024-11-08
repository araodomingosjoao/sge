<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;

/**
 * @OA\Info(
 *    title="API Documentation",
 *    version="1.0.0"
 * )
 */
trait CrudTrait
{
    protected $repository;

    /**
     * Convert Laravel validation rules to OpenAPI schema type
     */
    protected function getSchemaTypeFromRules(string $rules): array
    {
        $types = [
            'type' => 'string',
            'format' => null,
            'required' => false
        ];

        $rules = explode('|', $rules);
        
        if (in_array('required', $rules)) {
            $types['required'] = true;
        }

        if (in_array('numeric', $rules) || in_array('integer', $rules)) {
            $types['type'] = 'integer';
        }

        if (in_array('boolean', $rules)) {
            $types['type'] = 'boolean';
        }

        if (in_array('array', $rules)) {
            $types['type'] = 'array';
        }

        if (in_array('date', $rules)) {
            $types['format'] = 'date';
        }

        foreach ($rules as $rule) {
            if (strpos($rule, 'max:') === 0) {
                $types['maxLength'] = (int)substr($rule, 4);
            }
            if (strpos($rule, 'min:') === 0) {
                $types['minimum'] = (int)substr($rule, 4);
            }
        }

        return $types;
    }

    /**
     * Get OpenAPI schema for validation rules
     */
    protected function getSchemaFromRules(array $rules): array
    {
        $properties = [];
        $required = [];

        foreach ($rules as $field => $rule) {
            $types = $this->getSchemaTypeFromRules($rule);
            
            if ($types['required']) {
                $required[] = $field;
            }

            $property = [
                'type' => $types['type']
            ];

            if ($types['format']) {
                $property['format'] = $types['format'];
            }

            if (isset($types['maxLength'])) {
                $property['maxLength'] = $types['maxLength'];
            }

            if (isset($types['minimum'])) {
                $property['minimum'] = $types['minimum'];
            }

            $properties[$field] = $property;
        }

        return [
            'properties' => $properties,
            'required' => $required
        ];
    }

    /**
     * Create a new record
     * 
     * @OA\Post(
     *     path="/{resource}",
     *     summary="Create a new record",
     *     @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         required=true,
     *         description="Resource name"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body generated from storeValidationRules"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully"
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function create(Request $request)
    {
        $this->validateRequest($request, $this->storeValidationRules);

        try {
            $data = $request->all();
            $record = $this->repository->create($data);
            return ApiResponse::success(new $this->resource($record), 'Record created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Error creating record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get specific record
     * 
     * @OA\Get(
     *     path="/{resource}/{id}",
     *     @OA\Parameter(name="resource", in="path", required=true),
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function read($id)
    {
        $record = $this->repository->find($id);
        if ($record) {
            return ApiResponse::success($this->resourceDetails ? new $this->resourceDetails($record) : new $this->resource($record));
        }
        return ApiResponse::error('Record not found', 404);
    }

    /**
     * Update record
     * 
     * @OA\Put(
     *     path="/{resource}/{id}",
     *     @OA\Parameter(name="resource", in="path", required=true),
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body generated from updateValidationRules"
     *     ),
     *     @OA\Response(response=200, description="Updated successfully"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $this->applyUniqueValidationRules($id);
        $this->validateRequest($request, $this->updateValidationRules);

        try {
            $data = $request->all();
            $success = $this->repository->update($id, $data);
            if ($success) {
                return ApiResponse::success(new $this->resource($success), 'Record updated successfully');
            }
            return ApiResponse::error('Record not found', 404);
        } catch (\Exception $e) {
            return ApiResponse::error('Error updating record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete record
     * 
     * @OA\Delete(
     *     path="/{resource}/{id}",
     *     @OA\Parameter(name="resource", in="path", required=true),
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Deleted successfully"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function delete($id)
    {
        try {
            $success = $this->repository->delete($id);
            if ($success) {
                return ApiResponse::success(null, 'Record deleted successfully');
            }
            return ApiResponse::error('Record not found', 404);
        } catch (\Exception $e) {
            return ApiResponse::error('Error deleting record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get paginated list of records
     * 
     * @OA\Get(
     *     path="/{resource}",
     *     @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search term"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         schema={"type"="integer", "default"=15}
     *     ),
     *     @OA\Parameter(
     *         name="sort_column",
     *         in="query",
     *         schema={"type"="string", "default"="id"}
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         schema={"type"="string", "enum"={"asc","desc"}, "default"="asc"}
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->query('filters', []);
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 15);
        $sortColumn = $request->query('sort_column', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');

        try {
            $results = $this->repository->paginateWithFiltersAndSort($filters, $search, $perPage, $sortColumn, $sortDirection);

            return ApiResponse::success([
                'data' => $this->resource::collection($results->items()),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                    'last_page' => $results->lastPage(),
                    'next_page_url' => $results->nextPageUrl(),
                    'prev_page_url' => $results->previousPageUrl(),
                ],
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Error fetching records: ' . $e->getMessage(), 500);
        }
    }

    protected function applyUniqueValidationRules($id)
    {
        foreach ($this->uniqueFields as $field => $table) {
            $this->updateValidationRules[$field] = $this->updateValidationRules[$field] . '|unique:' . $table . ',' . $field . ',' . $id;
        }
    }

    protected function validateRequest(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            ApiResponse::error($validator->errors(), 422)->throwResponse();
        }
    }
}
