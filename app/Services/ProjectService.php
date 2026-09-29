<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectService
{
    /**
     * Create a new ProjectService instance.
     */
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * Get paginated projects.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->paginate($perPage, $filters);
    }

    /**
     * Get all projects.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, Project>
     */
    public function all(array $filters = []): Collection
    {
        return $this->projectRepository->all($filters);
    }

    /**
     * Find a project by ID or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Project
    {
        $project = $this->projectRepository->findById($id);

        if (! $project) {
            throw (new ModelNotFoundException)->setModel(Project::class, [$id]);
        }

        return $project;
    }

    /**
     * Create a new project.
     *
     * @param  array{name: string, active?: bool}  $data
     */
    public function create(array $data): Project
    {
        return $this->projectRepository->create([
            'name' => $data['name'],
            'active' => $data['active'] ?? true,
        ]);
    }

    /**
     * Update an existing project.
     *
     * @param  array{name?: string, active?: bool}  $data
     *
     * @throws ModelNotFoundException
     */
    public function update(int|Project $project, array $data): Project
    {
        $model = $project instanceof Project ? $project : $this->getById($project);

        return $this->projectRepository->update($model, $data);
    }

    /**
     * Delete a project.
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|Project $project): bool
    {
        $model = $project instanceof Project ? $project : $this->getById($project);

        return $this->projectRepository->delete($model);
    }
}
