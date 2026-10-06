<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatusTask\StoreStatusTaskRequest;
use App\Http\Requests\StatusTask\UpdateStatusTaskRequest;
use App\Http\Resources\StatusTaskResource;
use App\Services\StatusTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StatusTaskController extends Controller
{
    /**
     * Create a new StatusTaskController instance.
     */
    public function __construct(
        private readonly StatusTaskService $statusTaskService
    ) {}

    /**
     * Display a listing of statuses.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'active']);

        if ($request->boolean('all')) {
            return StatusTaskResource::collection($this->statusTaskService->all($filters));
        }

        $perPage = max(1, (int) ($request->input('per_page') ?: 10));

        return StatusTaskResource::collection($this->statusTaskService->paginate($perPage, $filters));
    }

    /**
     * Store a newly created status in storage.
     */
    public function store(StoreStatusTaskRequest $request): JsonResponse
    {
        $statusTask = $this->statusTaskService->create($request->validated());

        return response()->json([
            'message' => 'Status da tarefa criado com sucesso.',
            'data' => new StatusTaskResource($statusTask),
        ], 201);
    }

    /**
     * Display the specified status.
     */
    public function show(int $id): JsonResponse
    {
        $statusTask = $this->statusTaskService->getById($id);

        return response()->json([
            'data' => new StatusTaskResource($statusTask),
        ]);
    }

    /**
     * Update the specified status in storage.
     */
    public function update(UpdateStatusTaskRequest $request, int $id): JsonResponse
    {
        $statusTask = $this->statusTaskService->update($id, $request->validated());

        return response()->json([
            'message' => 'Status da tarefa atualizado com sucesso.',
            'data' => new StatusTaskResource($statusTask),
        ]);
    }

    /**
     * Remove the specified status from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->statusTaskService->delete($id);

        return response()->json([
            'message' => 'Status da tarefa removido com sucesso.',
        ]);
    }
}
