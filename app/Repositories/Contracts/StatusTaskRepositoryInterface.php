<?php

namespace App\Repositories\Contracts;

use App\Models\StatusTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StatusTaskRepositoryInterface
{
    /**
     * Get paginated statuses with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return LengthAwarePaginator<int, StatusTask>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Get all statuses with optional filters.
     *
     * @param  array{search?: string|null, active?: bool|string|null}  $filters
     * @return Collection<int, StatusTask>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a status by ID.
     */
    public function findById(int $id): ?StatusTask;

    /**
     * Find a status by slug.
     */
    public function findBySlug(string $slug): ?StatusTask;

    /**
     * Create a new status record.
     *
     * @param  array{name: string, slug?: string, active?: bool}  $data
     */
    public function create(array $data): StatusTask;

    /**
     * Update an existing status record.
     *
     * @param  array{name?: string, slug?: string, active?: bool}  $data
     */
    public function update(StatusTask $statusTask, array $data): StatusTask;

    /**
     * Delete a status record.
     */
    public function delete(StatusTask $statusTask): bool;
}
