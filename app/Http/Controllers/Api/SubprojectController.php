<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subproject\StoreSubprojectRequest;
use App\Http\Requests\Subproject\UpdateSubprojectRequest;
use App\Http\Resources\SubprojectResource;
use App\Services\SubprojectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubprojectController extends Controller
{
    /**
     * Create a new SubprojectController instance.
     */
    public function __construct(
        private readonly SubprojectService $subprojectService
    ) {}

    /**
     * Display a listing of subprojects.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'active', 'project_id', 'projeto_id']);
        if (! empty($filters['projeto_id']) && empty($filters['project_id'])) {
            $filters['project_id'] = $filters['projeto_id'];
        }

        if ($request->boolean('all')) {
            return SubprojectResource::collection($this->subprojectService->all($filters));
        }

        $perPage = max(1, (int) ($request->input('per_page') ?: 10));

        return SubprojectResource::collection($this->subprojectService->paginate($perPage, $filters));
    }

    /**
     * Store a newly created subproject in storage.
     */
    public function store(StoreSubprojectRequest $request): JsonResponse
    {
        $subproject = $this->subprojectService->create($request->validated());
        $subproject->load('project');

        return response()->json([
            'message' => 'Subprojeto criado com sucesso.',
            'data' => new SubprojectResource($subproject),
        ], 201);
    }

    /**
     * Display the specified subproject.
     */
    public function show(int $id): JsonResponse
    {
        $subproject = $this->subprojectService->getById($id);

        return response()->json([
            'data' => new SubprojectResource($subproject),
        ]);
    }

    /**
     * Update the specified subproject in storage.
     */
    public function update(UpdateSubprojectRequest $request, int $id): JsonResponse
    {
        $subproject = $this->subprojectService->update($id, $request->validated());
        $subproject->load('project');

        return response()->json([
            'message' => 'Subprojeto atualizado com sucesso.',
            'data' => new SubprojectResource($subproject),
        ]);
    }

    /**
     * Remove the specified subproject from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->subprojectService->delete($id);

        return response()->json([
            'message' => 'Subprojeto removido com sucesso.',
        ]);
    }
}
