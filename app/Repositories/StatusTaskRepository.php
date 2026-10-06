<?php

namespace App\Repositories;

use App\Models\StatusTask;
use App\Repositories\Contracts\StatusTaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StatusTaskRepository implements StatusTaskRepositoryInterface
{
    /**
     * Get paginated statuses with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, StatusTask>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(StatusTask::query(), $filters)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all statuses with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, StatusTask>
     */
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(StatusTask::query(), $filters)
            ->latest('id')
            ->get();
    }

    /**
     * Find a status by ID.
     */
    public function findById(int $id): ?StatusTask
    {
        return StatusTask::query()->find($id);
    }

    /**
     * Find a status by slug.
     */
    public function findBySlug(string $slug): ?StatusTask
    {
        return StatusTask::query()->where('slug', $slug)->first();
    }

    /**
     * Create a new status record.
     *
     * @param  array{name: string, slug?: string, active?: bool}  $data
     */
    public function create(array $data): StatusTask
    {
        return StatusTask::query()->create($data);
    }

    /**
     * Update an existing status record.
     *
     * @param  array{name?: string, slug?: string, active?: bool}  $data
     */
    public function update(StatusTask $statusTask, array $data): StatusTask
    {
        $statusTask->update($data);

        return $statusTask->fresh() ?? $statusTask;
    }

    /**
     * Delete a status record.
     */
    public function delete(StatusTask $statusTask): bool
    {
        return (bool) $statusTask->delete();
    }

    /**
     * Apply filter criteria to the query builder.
     *
     * @param  Builder<StatusTask>  $query
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Builder<StatusTask>
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
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
