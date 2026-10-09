<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sprint\StoreSprintRequest;
use App\Http\Requests\Sprint\UpdateSprintRequest;
use App\Http\Resources\SprintResource;
use App\Services\SprintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SprintController extends Controller
{
    /**
     * Create a new SprintController instance.
     */
    public function __construct(
        private readonly SprintService $sprintService
    ) {}

    /**
     * Display a listing of sprints.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'user_id', 'subproject_id', 'year', 'month']);

        if ($request->has('ano') && ! isset($filters['year'])) {
            $filters['year'] = $request->input('ano');
        }

        if ($request->has('mes') && ! isset($filters['month'])) {
            $filters['month'] = $request->input('mes');
        }

        if ($request->boolean('all')) {
            return SprintResource::collection($this->sprintService->all($filters));
        }

        $perPage = max(1, (int) ($request->input('per_page') ?: 10));

        return SprintResource::collection($this->sprintService->paginate($perPage, $filters));
    }

    /**
     * Store a newly created sprint in storage.
     */
    public function store(StoreSprintRequest $request): JsonResponse
    {
        $sprint = $this->sprintService->create($request->validated());
        $sprint->load(['user', 'subproject']);

        return response()->json([
            'message' => 'Sprint criada com sucesso.',
            'data' => new SprintResource($sprint),
        ], 201);
    }

    /**
     * Display the specified sprint.
     */
    public function show(int $id): JsonResponse
    {
        $sprint = $this->sprintService->getById($id);

        return response()->json([
            'data' => new SprintResource($sprint),
        ]);
    }

    /**
     * Update the specified sprint in storage.
     */
    public function update(UpdateSprintRequest $request, int $id): JsonResponse
    {
        $sprint = $this->sprintService->update($id, $request->validated());
        $sprint->load(['user', 'subproject']);

        return response()->json([
            'message' => 'Sprint atualizada com sucesso.',
            'data' => new SprintResource($sprint),
        ]);
    }

    /**
     * Remove the specified sprint from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->sprintService->delete($id);

        return response()->json([
            'message' => 'Sprint removida com sucesso.',
        ]);
    }
}
