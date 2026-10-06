<?php

namespace App\Services;

use App\Models\StatusTask;
use App\Repositories\Contracts\StatusTaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class StatusTaskService
{
    /**
     * Create a new StatusTaskService instance.
     */
    public function __construct(
        private readonly StatusTaskRepositoryInterface $statusTaskRepository
    ) {}

    /**
     * Get paginated statuses.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, StatusTask>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->statusTaskRepository->paginate($perPage, $filters);
    }

    /**
     * Get all statuses.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, StatusTask>
     */
    public function all(array $filters = []): Collection
    {
        return $this->statusTaskRepository->all($filters);
    }

    /**
     * Find a status by ID or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): StatusTask
    {
        $statusTask = $this->statusTaskRepository->findById($id);

        if (! $statusTask) {
            throw (new ModelNotFoundException)->setModel(StatusTask::class, [$id]);
        }

        return $statusTask;
    }

    /**
     * Find a status by slug or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getBySlug(string $slug): StatusTask
    {
        $statusTask = $this->statusTaskRepository->findBySlug($slug);

        if (! $statusTask) {
            throw (new ModelNotFoundException)->setModel(StatusTask::class, [$slug]);
        }

        return $statusTask;
    }

    /**
     * Create a new status.
     *
     * @param  array{name: string, slug?: string|null, active?: bool}  $data
     */
    public function create(array $data): StatusTask
    {
        $slug = ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        return $this->statusTaskRepository->create([
            'name' => $data['name'],
            'slug' => $slug,
            'active' => $data['active'] ?? true,
        ]);
    }

    /**
     * Update an existing status.
     *
     * @param  array{name?: string, slug?: string|null, active?: bool}  $data
     *
     * @throws ModelNotFoundException
     */
    public function update(int|StatusTask $statusTask, array $data): StatusTask
    {
        $model = $statusTask instanceof StatusTask ? $statusTask : $this->getById($statusTask);

        if (array_key_exists('slug', $data) && ! empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        return $this->statusTaskRepository->update($model, $data);
    }

    /**
     * Delete a status.
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|StatusTask $statusTask): bool
    {
        $model = $statusTask instanceof StatusTask ? $statusTask : $this->getById($statusTask);

        return $this->statusTaskRepository->delete($model);
    }
}
