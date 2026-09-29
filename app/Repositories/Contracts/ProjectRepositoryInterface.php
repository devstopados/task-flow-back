<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    /**
     * Get paginated projects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get all projects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, Project>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a project by ID.
     */
    public function findById(int $id): ?Project;

    /**
     * Create a new project record.
     *
     * @param  array{name: string, active?: bool}  $data
     */
    public function create(array $data): Project;

    /**
     * Update an existing project record.
     *
     * @param  array{name?: string, active?: bool}  $data
     */
    public function update(Project $project, array $data): Project;

    /**
     * Delete a project record.
     */
    public function delete(Project $project): bool;
}
