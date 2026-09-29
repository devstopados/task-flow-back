export default {
    "/projects": {
        get: {
            summary: "Listar projetos",
            description: "Retorna a listagem de projetos com suporte a paginação e filtros.",
            operationId: "projects.index",
            tags: ["Projects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "search",
                    in: "query",
                    required: false,
                    description: "Filtrar por nome do projeto",
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
                    description: "Retornar todos os projetos sem paginação",
                    schema: { type: "boolean" },
                },
                {
                    name: "per_page",
                    in: "query",
                    required: false,
                    description: "Quantidade de itens por página (padrão: 15)",
                    schema: { type: "integer", default: 15 },
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
                    description: "Lista de projetos retornada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/ProjectListResponse",
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
            summary: "Criar novo projeto",
            description: "Cria um novo projeto no banco de dados.",
            operationId: "projects.store",
            tags: ["Projects"],
            security: [
                { bearerAuth: [] },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/StoreProjectRequest",
                        },
                    },
                },
            },
            responses: {
                201: {
                    description: "Projeto criado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/ProjectSingleResponse",
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
    "/projects/{id}": {
        get: {
            summary: "Detalhes do projeto",
            description: "Retorna os detalhes de um projeto específico.",
            operationId: "projects.show",
            tags: ["Projects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do projeto",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Dados do projeto",
                    content: {
                        "application/json": {
                            schema: {
                                type: "object",
                                properties: {
                                    data: {
                                        $ref: "#/components/schemas/Project",
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
                    description: "Projeto não encontrado",
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
            summary: "Atualizar projeto",
            description: "Atualiza os dados de um projeto existente.",
            operationId: "projects.update",
            tags: ["Projects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do projeto",
                    schema: { type: "integer" },
                },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/UpdateProjectRequest",
                        },
                    },
                },
            },
            responses: {
                200: {
                    description: "Projeto atualizado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/ProjectSingleResponse",
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
                    description: "Projeto não encontrado",
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
            summary: "Remover projeto",
            description: "Remove um projeto existente.",
            operationId: "projects.destroy",
            tags: ["Projects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do projeto",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Projeto removido com sucesso",
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
                    description: "Projeto não encontrado",
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
