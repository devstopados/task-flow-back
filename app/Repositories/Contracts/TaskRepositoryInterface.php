<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    /**
     * Get paginated tasks with optional filters.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Get all tasks with optional filters.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return Collection<int, Task>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a task by ID.
     */
    public function findById(int $id): ?Task;

    /**
     * Create a new task record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Task;

    /**
     * Update an existing task record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Task $task, array $data): Task;

    /**
     * Delete a task record.
     */
    public function delete(Task $task): bool;
}
