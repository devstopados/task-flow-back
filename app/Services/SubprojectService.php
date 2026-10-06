<?php

namespace App\Services;

use App\Models\Subproject;
use App\Repositories\Contracts\SubprojectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubprojectService
{
    /**
     * Create a new SubprojectService instance.
     */
    public function __construct(
        private readonly SubprojectRepositoryInterface $subprojectRepository
    ) {}

    /**
     * Get paginated subprojects.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Subproject>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->subprojectRepository->paginate($perPage, $filters);
    }

    /**
     * Get all subprojects.
     *
     * @param  array{search?: string|null, active?: bool|string|null, project_id?: int|string|null}  $filters
     * @return Collection<int, Subproject>
     */
    public function all(array $filters = []): Collection
    {
        return $this->subprojectRepository->all($filters);
    }

    /**
     * Find a subproject by ID or fail.
     *
     * @throws ModelNotFoundException
     */
    public function getById(int $id): Subproject
    {
        $subproject = $this->subprojectRepository->findById($id);

        if (! $subproject) {
            throw (new ModelNotFoundException)->setModel(Subproject::class, [$id]);
        }

        return $subproject;
    }

    /**
     * Create a new subproject.
     *
     * @param  array{project_id: int, name: string, active?: bool}  $data
     */
    public function create(array $data): Subproject
    {
        return $this->subprojectRepository->create([
            'project_id' => (int) $data['project_id'],
            'name' => $data['name'],
            'active' => $data['active'] ?? true,
        ]);
    }

    /**
     * Update an existing subproject.
     *
     * @param  array{project_id?: int, name?: string, active?: bool}  $data
     *
     * @throws ModelNotFoundException
     */
    public function update(int|Subproject $subproject, array $data): Subproject
    {
        $model = $subproject instanceof Subproject ? $subproject : $this->getById($subproject);

        return $this->subprojectRepository->update($model, $data);
    }

    /**
     * Delete a subproject.
     *
     * @throws ModelNotFoundException
     */
    public function delete(int|Subproject $subproject): bool
    {
        $model = $subproject instanceof Subproject ? $subproject : $this->getById($subproject);

        return $this->subprojectRepository->delete($model);
    }
}
