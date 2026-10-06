<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Create a new TaskService instance.
     */
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {}

    /**
     * Get paginated tasks.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Task>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->paginate($perPage, $filters);
    }

    /**
     * Get all tasks.
     *
     * @param  array{search?: string|null, subproject_id?: int|string|null, status_id?: int|string|null}  $filters
     * @return Collection<int, Task>
     */
    public function all(array $filters = []): Collection
    {
        return $this->taskRepository->all($filters);
    }

    /**
     * Find a task by ID or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Task
    {
        $task = $this->taskRepository->findById($id);

        if (! $task) {
            throw (new ModelNotFoundException)->setModel(Task::class, [$id]);
        }

        return $task;
    }

    /**
     * Create a new task.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Task
    {
        return $this->taskRepository->create([
            'code' => $data['code'] ?? null,
            'name' => $data['name'],
            'start_date' => $data['start_date'] ?? now(),
            'end_date' => $data['end_date'] ?? null,
            'hours' => isset($data['hours']) ? (float) $data['hours'] : null,
            'branch' => $data['branch'] ?? null,
            'link' => $data['link'] ?? null,
            'status_id' => (int) $data['status_id'],
            'subproject_id' => isset($data['subproject_id']) && $data['subproject_id'] !== null ? (int) $data['subproject_id'] : null,
        ]);
    }

    /**
     * Update an existing task.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ModelNotFoundException
     */
    public function update(int|Task $task, array $data): Task
    {
        $model = $task instanceof Task ? $task : $this->getById($task);

        return $this->taskRepository->update($model, $data);
    }

    /**
     * Delete a task.
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|Task $task): bool
    {
        $model = $task instanceof Task ? $task : $this->getById($task);

        return $this->taskRepository->delete($model);
    }
}
