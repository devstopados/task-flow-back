<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Get paginated projects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(Project::query(), $filters)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all projects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, Project>
     */
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(Project::query(), $filters)
            ->latest('id')
            ->get();
    }

    /**
     * Find a project by ID.
     */
    public function findById(int $id): ?Project
    {
        return Project::query()->find($id);
    }

    /**
     * Create a new project record.
     *
     * @param  array{name: string, active?: bool}  $data
     */
    public function create(array $data): Project
    {
        return Project::query()->create($data);
    }

    /**
     * Update an existing project record.
     *
     * @param  array{name?: string, active?: bool}  $data
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh() ?? $project;
    }

    /**
     * Delete a project record.
     */
    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }

    /**
     * Apply filter criteria to the query builder.
     *
     * @param  Builder<Project>  $query
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Builder<Project>
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        if (array_key_exists('active', $filters) && $filters['active'] !== null && $filters['active'] !== '') {
            $active = filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($active !== null) {
                $query->where('active', $active);
            }
        }

        return $query;
    }
}
