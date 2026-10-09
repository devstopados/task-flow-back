<?php

namespace App\Repositories\Contracts;

use App\Models\Sprint;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SprintRepositoryInterface
{
    /**
     * Get paginated sprints with optional filters.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return LengthAwarePaginator<int, Sprint>
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    /**
     * Get all sprints with optional filters.
     *
     * @param  array{search?: string|null, user_id?: int|string|null, subproject_id?: int|string|null, year?: int|string|null, month?: int|string|null}  $filters
     * @return Collection<int, Sprint>
     */
    public function all(array $filters = []): Collection;

    /**
     * Find a sprint by ID.
     */
    public function findById(int $id): ?Sprint;

    /**
     * Create a new sprint record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Sprint;

    /**
     * Update an existing sprint record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Sprint $sprint, array $data): Sprint;

    /**
     * Delete a sprint record.
     */
    public function delete(Sprint $sprint): bool;
}
