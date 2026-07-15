import user from './docs/user'
export default {
    openapi: '3.0.0',
    info: {
        title: 'Task Flow API',
        description: 'API documentation for the Task Flow project.',
        version: '1.0.0',
        contact: {
            name: 'Support',
        },
    },
    servers: [
        {
            url: '/api',
            description: 'Local API server',
        },
    ],
    paths: {
        ...user
    },
    components: {
        securitySchemes: {
            bearerAuth: {
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'token',
            },
        },
    },
};
