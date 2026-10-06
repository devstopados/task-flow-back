<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Create a new TaskController instance.
     */
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'subproject_id', 'status_id']);

        if ($request->boolean('all')) {
            return TaskResource::collection($this->taskService->all($filters));
        }

        $perPage = max(1, (int) ($request->input('per_page') ?: 10));

        return TaskResource::collection($this->taskService->paginate($perPage, $filters));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());
        $task->load('subproject');

        return response()->json([
            'message' => 'Tarefa criada com sucesso.',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->getById($id);

        return response()->json([
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->update($id, $request->validated());
        $task->load('subproject');

        return response()->json([
            'message' => 'Tarefa atualizada com sucesso.',
            'data' => new TaskResource($task),
        ]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id);

        return response()->json([
            'message' => 'Tarefa removida com sucesso.',
        ]);
    }
}
