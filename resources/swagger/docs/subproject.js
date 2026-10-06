export default {
    "/subprojects": {
        get: {
            summary: "Listar subprojetos",
            description: "Retorna a listagem de subprojetos com suporte a paginação e filtros.",
            operationId: "subprojects.index",
            tags: ["Subprojects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "search",
                    in: "query",
                    required: false,
                    description: "Filtrar por nome do subprojeto",
                    schema: { type: "string" },
                },
                {
                    name: "project_id",
                    in: "query",
                    required: false,
                    description: "Filtrar por ID do projeto",
                    schema: { type: "integer" },
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
                    description: "Retornar todos os subprojetos sem paginação",
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
                    description: "Lista de subprojetos retornada com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/SubprojectListResponse",
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
            summary: "Criar novo subprojeto",
            description: "Cria um novo subprojeto no banco de dados.",
            operationId: "subprojects.store",
            tags: ["Subprojects"],
            security: [
                { bearerAuth: [] },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/StoreSubprojectRequest",
                        },
                    },
                },
            },
            responses: {
                201: {
                    description: "Subprojeto criado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/SubprojectSingleResponse",
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
    "/subprojects/{id}": {
        get: {
            summary: "Detalhes do subprojeto",
            description: "Retorna os detalhes de um subprojeto específico.",
            operationId: "subprojects.show",
            tags: ["Subprojects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do subprojeto",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Dados do subprojeto",
                    content: {
                        "application/json": {
                            schema: {
                                type: "object",
                                properties: {
                                    data: {
                                        $ref: "#/components/schemas/Subproject",
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
                    description: "Subprojeto não encontrado",
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
            summary: "Atualizar subprojeto",
            description: "Atualiza os dados de um subprojeto existente.",
            operationId: "subprojects.update",
            tags: ["Subprojects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do subprojeto",
                    schema: { type: "integer" },
                },
            ],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/UpdateSubprojectRequest",
                        },
                    },
                },
            },
            responses: {
                200: {
                    description: "Subprojeto atualizado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/SubprojectSingleResponse",
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
                    description: "Subprojeto não encontrado",
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
            summary: "Remover subprojeto",
            description: "Remove um subprojeto existente.",
            operationId: "subprojects.destroy",
            tags: ["Subprojects"],
            security: [
                { bearerAuth: [] },
            ],
            parameters: [
                {
                    name: "id",
                    in: "path",
                    required: true,
                    description: "Identificador do subprojeto",
                    schema: { type: "integer" },
                },
            ],
            responses: {
                200: {
                    description: "Subprojeto removido com sucesso",
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
                    description: "Subprojeto não encontrado",
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
