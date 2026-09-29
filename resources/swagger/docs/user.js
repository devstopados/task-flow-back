export default {
    "/user": {
        get: {
            summary: "Mostrar usuário logado",
            operationId: "user.get",
            tags: ["User"],
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
