<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;

trait CrudTrait
{
    protected $repository;

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

    public function read($id)
    {
        $record = $this->repository->find($id);
        if ($record) {
            return ApiResponse::success($this->resourceDetails ? new $this->resourceDetails($record) : new $this->resource($record));
        }
        return ApiResponse::error('Record not found', 404);
    }

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
