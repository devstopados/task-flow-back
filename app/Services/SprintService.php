<?php

namespace App\Services;

use App\Models\Sprint;
use App\Repositories\Contracts\SprintRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SprintService
{
    /**
     * Create a new SprintService instance.
     */
    public function __construct(
        private readonly SprintRepositoryInterface $sprintRepository
    ) {}

    /**
     * Get paginated sprints.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Sprint>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->sprintRepository->paginate($perPage, $filters);
    }

    /**
     * Get all sprints.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return Collection<int, Sprint>
     */
    public function all(array $filters = []): Collection
    {
        return $this->sprintRepository->all($filters);
    }

    /**
     * Find a sprint by ID or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Sprint
    {
        $sprint = $this->sprintRepository->findById($id);

        if (! $sprint) {
            throw (new ModelNotFoundException)->setModel(Sprint::class, [$id]);
        }

        return $sprint;
    }

    /**
     * Create a new sprint.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Sprint
    {
        return $this->sprintRepository->create([
            'user_id' => (int) $data['user_id'],
            'subproject_id' => (int) $data['subproject_id'],
            'name' => $data['name'],
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
        ]);
    }

    /**
     * Update an existing sprint.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ModelNotFoundException
     */
    public function update(int|Sprint $sprint, array $data): Sprint
    {
        $model = $sprint instanceof Sprint ? $sprint : $this->getById($sprint);

        return $this->sprintRepository->update($model, $data);
    }

    /**
     * Delete a sprint.
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|Sprint $sprint): bool
    {
        $model = $sprint instanceof Sprint ? $sprint : $this->getById($sprint);

        return $this->sprintRepository->delete($model);
    }
}
