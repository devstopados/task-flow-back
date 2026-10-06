<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * Create a new ProjectController instance.
     */
    public function __construct(
        private readonly ProjectService $projectService
    ) {}

    /**
     * Display a listing of projects.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'active']);

        if ($request->boolean('all')) {
            return ProjectResource::collection($this->projectService->all($filters));
        }

        $perPage = max(1, (int) ($request->input('per_page') ?: 10));

        return ProjectResource::collection($this->projectService->paginate($perPage, $filters));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->create($request->validated());

        return response()->json([
            'message' => 'Projeto criado com sucesso.',
            'data' => new ProjectResource($project),
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(int $id): JsonResponse
    {
        $project = $this->projectService->getById($id);

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        $project = $this->projectService->update($id, $request->validated());

        return response()->json([
            'message' => 'Projeto atualizado com sucesso.',
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->projectService->delete($id);

        return response()->json([
            'message' => 'Projeto removido com sucesso.',
        ]);
    }
}
