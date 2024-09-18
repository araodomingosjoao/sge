<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    public function create(array $data): Model;
    public function find(int $id): ?Model;
    public function update(int $id, array $data): Model|bool;
    public function delete(int $id): bool;
    public function paginateWithFiltersAndSort(array $filters, string $search, int $perPage, string $sortColumn, string $sortDirection): LengthAwarePaginator;
}