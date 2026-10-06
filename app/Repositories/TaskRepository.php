<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    /**
     * Get paginated tasks with optional filters.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(Task::query(), $filters)
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Get all tasks with optional filters.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return Collection<int, Task>
     */
    public function all(array $filters = []): Collection
    {
        return $this->applyFilters(Task::query(), $filters)
            ->latest('id')
            ->get();
    }

    /**
     * Find a task by ID.
     */
    public function findById(int $id): ?Task
    {
        return Task::query()->with('subproject')->find($id);
    }

    /**
     * Create a new task record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Task
    {
        return Task::query()->create($data);
    }

    /**
     * Update an existing task record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh() ?? $task;
    }

    /**
     * Delete a task record.
     */
    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }

    /**
     * Apply filter criteria to the query builder.
     *
     * @param  Builder<Task>  $query
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return Builder<Task>
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->with('subproject');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['subproject_id'])) {
            $query->where('subproject_id', (int) $filters['subproject_id']);
        }

        if (! empty($filters['status_id'])) {
            $query->where('status_id', (int) $filters['status_id']);
        }

        return $query;
    }
}
