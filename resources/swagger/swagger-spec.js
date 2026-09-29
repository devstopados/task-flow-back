import auth from './docs/auth';
import user from './docs/user';

export default {
    openapi: '3.0.0',
    info: {
        title: 'Task Flow API',
        description: 'Documentação da API para o projeto Task Flow.',
        version: '1.0.0',
        contact: {
            name: 'Suporte Task Flow',
        },
    },
    servers: [
        {
            url: '/api',
            description: 'Servidor Local da API',
        },
    ],
    tags: [
        {
            name: 'Auth',
            description: 'Endpoints de autenticação e sessão',
        },
        {
            name: 'User',
            description: 'Endpoints de perfil de usuário',
        },
    ],
    paths: {
        ...auth,
        ...user,
    },
    components: {
        securitySchemes: {
            bearerAuth: {
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT',
                description: 'Insira o token Sanctum no formato: Bearer {token}',
            },
        },
        schemas: {
            User: {
                type: 'object',
                properties: {
                    id: { type: 'integer', example: 1 },
                    name: { type: 'string', example: 'Ana Freitas' },
                    email: { type: 'string', format: 'email', example: 'ana@example.com' },
                    created_at: { type: 'string', format: 'date-time', example: '2026-09-29T16:00:00.000000Z' },
                    updated_at: { type: 'string', format: 'date-time', example: '2026-09-29T16:00:00.000000Z' },
                },
            },
            RegisterRequest: {
                type: 'object',
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: {
                    name: { type: 'string', example: 'Ana Freitas' },
                    email: { type: 'string', format: 'email', example: 'ana@example.com' },
                    password: { type: 'string', format: 'password', example: 'Password123!' },
                    password_confirmation: { type: 'string', format: 'password', example: 'Password123!' },
                },
            },
            LoginRequest: {
                type: 'object',
                required: ['email', 'password'],
                properties: {
                    email: { type: 'string', format: 'email', example: 'ana@example.com' },
                    password: { type: 'string', format: 'password', example: 'Password123!' },
                },
            },
            AuthSuccessResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Login successful.' },
                    user: { $ref: '#/components/schemas/User' },
                    token: { type: 'string', example: '1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx' },
                },
            },
            UserProfileResponse: {
                type: 'object',
                properties: {
                    user: { $ref: '#/components/schemas/User' },
                },
            },
            MessageResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Logged out successfully.' },
                },
            },
            ValidationError: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Os dados fornecidos são inválidos.' },
                    errors: {
                        type: 'object',
                        additionalProperties: {
                            type: 'array',
                            items: { type: 'string' },
                        },
                        example: {
                            email: ['The email has already been taken.'],
                        },
                    },
                },
            },
        },
    },
};
