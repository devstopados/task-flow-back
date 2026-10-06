<?php

namespace App\Repositories\Contracts;

use App\Models\Subproject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SubprojectRepositoryInterface
{
    /**
     * Get paginated subprojects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Subproject>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Get all subprojects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return Collection<int, Subproject>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a subproject by ID.
     */
    public function findById(int $id): ?Subproject;

    /**
     * Create a new subproject record.
     *
     * @param  array{project_id: int, name: string, active?: bool}  $data
     */
    public function create(array $data): Subproject;

    /**
     * Update an existing subproject record.
     *
     * @param  array{project_id?: int, name?: string, active?: bool}  $data
     */
    public function update(Subproject $subproject, array $data): Subproject;

    /**
     * Delete a subproject record.
     */
    public function delete(Subproject $subproject): bool;
}
