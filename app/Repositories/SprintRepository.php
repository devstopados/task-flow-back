<?php

namespace App\Repositories;

use App\Models\Sprint;
use App\Repositories\Contracts\SprintRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SprintRepository implements SprintRepositoryInterface
{
    /**
     * Get paginated sprints with optional filters.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Sprint>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(Sprint::query(), $filters)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all sprints with optional filters.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return Collection<int, Sprint>
     */
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(Sprint::query(), $filters)
            ->latest('id')
            ->get();
    }

    /**
     * Find a sprint by ID.
     */
    public function findById(int $id): ?Sprint
    {
        return Sprint::query()->with(['user', 'subproject'])->find($id);
    }

    /**
     * Create a new sprint record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Sprint
    {
        return Sprint::query()->create($data);
    }

    /**
     * Update an existing sprint record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Sprint $sprint, array $data): Sprint
    {
        $sprint->update($data);

        return $sprint->fresh() ?? $sprint;
    }

    /**
     * Delete a sprint record.
     */
    public function delete(Sprint $sprint): bool
    {
        return (bool) $sprint->delete();
    }

    /**
     * Apply filter criteria to the query builder.
     *
     * @param  Builder<Sprint>  $query
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return Builder<Sprint>
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->with(['user', 'subproject']);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('name', 'like', "%{$search}%");
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (! empty($filters['subproject_id'])) {
            $query->where('subproject_id', (int) $filters['subproject_id']);
        }

        if (! empty($filters['year'])) {
            $query->where('year', (int) $filters['year']);
        }

        if (! empty($filters['month'])) {
            $query->where('month', (int) $filters['month']);
        }

        return $query;
    }
}
