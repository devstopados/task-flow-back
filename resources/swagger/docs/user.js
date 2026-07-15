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
                    description: "Successful response",
                    content: {
                        "application/json": {
                        },
                    },
                },
            },
        },
    },
};
