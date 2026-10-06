export default {
    "/status-tasks": {
        get: {
            summary: "Listar status de tarefas",
            description: "Retorna a listagem de status de tarefas com suporte a paginação e filtros.",
            operationId: "statusTasks.index",
            tags: ["StatusTasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "search",
                    in: "query",
                    required: false,
                    description: "Filtrar por nome ou slug do status",
                    schema: { type: "string" },
                },
                {
                    name: "active",
                    in: "query",
                    required: false,
                    description: "Filtrar por status ativo (true/false)",
                    schema: { type: "boolean" },
                },
                {
                    name: "all",
                    in: "query",
                    required: false,
                    description: "Retornar todos os status sem paginação",
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
                    description: "Lista de status retornada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/StatusTaskListResponse",
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
            summary: "Criar novo status de tarefa",
            description: "Cria um novo status de tarefa no banco de dados.",
            operationId: "statusTasks.store",
            tags: ["StatusTasks"],
            security: [
                { bearerAuth: [] },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/StoreStatusTaskRequest",
                        },
                    },
                },
            },
            responses: {
                201: {
                    description: "Status criado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/StatusTaskSingleResponse",
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
    "/status-tasks/{id}": {
        get: {
            summary: "Detalhes do status de tarefa",
            description: "Retorna os detalhes de um status de tarefa específico.",
            operationId: "statusTasks.show",
            tags: ["StatusTasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do status",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Dados do status",
                    content: {
                        "application/json": {
                            schema: {
                                type: "object",
                                properties: {
                                    data: {
                                        $ref: "#/components/schemas/StatusTask",
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
                    description: "Status não encontrado",
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
            summary: "Atualizar status de tarefa",
            description: "Atualiza os dados de um status de tarefa existente.",
            operationId: "statusTasks.update",
            tags: ["StatusTasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do status",
                    schema: { type: "integer" },
                },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/UpdateStatusTaskRequest",
                        },
                    },
                },
            },
            responses: {
                200: {
                    description: "Status atualizado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/StatusTaskSingleResponse",
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
                    description: "Status não encontrado",
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
            summary: "Remover status de tarefa",
            description: "Remove um status de tarefa existente.",
            operationId: "statusTasks.destroy",
            tags: ["StatusTasks"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do status",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Status removido com sucesso",
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
                    description: "Status não encontrado",
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
