export default {
    "/auth/register": {
        post: {
            summary: "Registrar novo usuário",
            operationId: "auth.register",
            tags: ["Auth"],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/RegisterRequest",
                        },
                    },
                },
            },
            responses: {
                201: {
                    description: "Usuário registrado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/AuthSuccessResponse",
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
    "/auth/login": {
        post: {
            summary: "Autenticar usuário",
            operationId: "auth.login",
            tags: ["Auth"],
            requestBody: {
                required: true,
                content: {
                    "application/json": {
                        schema: {
                            $ref: "#/components/schemas/LoginRequest",
                        },
                    },
                },
            },
            responses: {
                200: {
                    description: "Login realizado com sucesso",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/AuthSuccessResponse",
                            },
                        },
                    },
                },
                422: {
                    description: "Credenciais inválidas ou erro de validação",
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
    "/auth/logout": {
        post: {
            summary: "Encerrar sessão do usuário (revogar token)",
            operationId: "auth.logout",
            tags: ["Auth"],
            security: [
                { bearerAuth: [] },
            ],
            responses: {
                200: {
                    description: "Logout realizado com sucesso",
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
            },
        },
    },
    "/auth/me": {
        get: {
            summary: "Obter perfil do usuário autenticado",
            operationId: "auth.me",
            tags: ["Auth"],
            security: [
                { bearerAuth: [] },
            ],
            responses: {
                200: {
                    description: "Dados do perfil do usuário autenticado",
                    content: {
                        "application/json": {
                            schema: {
                                $ref: "#/components/schemas/UserProfileResponse",
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
    },
};
