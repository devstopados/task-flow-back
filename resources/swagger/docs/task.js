export default {
    "/tasks": {
        get: {
            summary: "Listar tarefas",
            description: "Retorna a listagem de tarefas com suporte a paginação e filtros.",
            operationId: "tasks.index",
            tags: ["Tasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "search",
                    in: "query",
                    required: false,
                    description: "Filtrar por nome ou código da tarefa",
                    schema: { type: "string" },
                },
                {
                    name: "subproject_id",
                    in: "query",
                    required: false,
                    description: "Filtrar por ID do subprojeto",
                    schema: { type: "integer" },
                },
                {
                    name: "status_id",
                    in: "query",
                    required: false,
                    description: "Filtrar por ID do status",
                    schema: { type: "integer" },
                },
                {
                    name: "all",
                    in: "query",
                    required: false,
                    description: "Retornar todas as tarefas sem paginação",
                    schema: { type: "boolean" },
                },
                {
                    name: "per_page",
                    in: "query",
                    required: false,
                    description: "Quantidade de itens por página (padrão: 10)",
                    schema: { type: "integer", default: 10 },
                },
                {
                    name: "page",
                    in: "query",
                    required: false,
                    description: "Número da página",
                    schema: { type: "integer", default: 1 },
                },
            ],
            responses: {
                200: {
                    description: "Lista de tarefas retornada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/TaskListResponse",
                            },
                        },
                    },
                },
                401: {
                    description: "Não autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
            },
        },
        post: {
            summary: "Criar nova tarefa",
            description: "Cria uma nova tarefa no banco de dados.",
            operationId: "tasks.store",
            tags: ["Tasks"],
            security: [
                { bearerAuth: [] },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/StoreTaskRequest",
                        },
                    },
                },
            },
            responses: {
                201: {
                    description: "Tarefa criada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/TaskSingleResponse",
                            },
                        },
                    },
                },
                401: {
                    description: "Não autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                422: {
                    description: "Erro de validação dos dados",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/ValidationError",
                            },
                        },
                    },
                },
            },
        },
    },
    "/tasks/{id}": {
        get: {
            summary: "Detalhes da tarefa",
            description: "Retorna os detalhes de uma tarefa específica.",
            operationId: "tasks.show",
            tags: ["Tasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador da tarefa",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Dados da tarefa",
                    content: {
                        "application/json": {
                            schema: {
                                type: "object",
                                properties: {
                                    data: {
                                        $ref: "#/components/schemas/Task",
                                    },
                                },
                            },
                        },
                    },
                },
                401: {
                    description: "Não autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                404: {
                    description: "Tarefa não encontrada",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
            },
        },
        put: {
            summary: "Atualizar tarefa",
            description: "Atualiza os dados de uma tarefa existente.",
            operationId: "tasks.update",
            tags: ["Tasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador da tarefa",
                    schema: { type: "integer" },
                },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/UpdateTaskRequest",
                        },
                    },
                },
            },
            responses: {
                200: {
                    description: "Tarefa atualizada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/TaskSingleResponse",
                            },
                        },
                    },
                },
                401: {
                    description: "Não autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                404: {
                    description: "Tarefa não encontrada",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                422: {
                    description: "Erro de validação dos dados",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/ValidationError",
                            },
                        },
                    },
                },
            },
        },
        delete: {
            summary: "Remover tarefa",
            description: "Remove uma tarefa existente.",
            operationId: "tasks.destroy",
            tags: ["Tasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador da tarefa",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Tarefa removida com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                401: {
                    description: "Não autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
                404: {
                    description: "Tarefa não encontrada",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/MessageResponse",
                            },
                        },
                    },
                },
            },
        },
    },
};
