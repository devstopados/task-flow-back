<?php

namespace App\Repositories;

use App\Models\Subproject;
use App\Repositories\Contracts\SubprojectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubprojectRepository implements SubprojectRepositoryInterface
{
    /**
     * Get paginated subprojects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Subproject>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(Subproject::query(), $filters)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all subprojects with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return Collection<int, Subproject>
     */
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(Subproject::query(), $filters)
            ->latest('id')
            ->get();
    }

    /**
     * Find a subproject by ID.
     */
    public function findById(int $id): ?Subproject
    {
        return Subproject::query()->with('project')->find($id);
    }

    /**
     * Create a new subproject record.
     *
     * @param  array{project_id: int, name: string, active?: bool}  $data
     */
    public function create(array $data): Subproject
    {
        return Subproject::query()->create($data);
    }

    /**
     * Update an existing subproject record.
     *
     * @param  array{project_id?: int, name?: string, active?: bool}  $data
     */
    public function update(Subproject $subproject, array $data): Subproject
    {
        $subproject->update($data);

        return $subproject->fresh() ?? $subproject;
    }

    /**
     * Delete a subproject record.
     */
    public function delete(Subproject $subproject): bool
    {
        return (bool) $subproject->delete();
    }

    /**
     * Apply filter criteria to the query builder.
     *
     * @param  Builder<Subproject>  $query
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return Builder<Subproject>
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->with('project');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        if (! empty($filters['project_id'])) {
            $query->where('project_id', (int) $filters['project_id']);
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
