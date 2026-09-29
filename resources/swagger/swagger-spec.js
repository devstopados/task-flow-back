import auth from './docs/auth';
import project from './docs/project';
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
        {
            name: 'Projects',
            description: 'Endpoints de gerenciamento de projetos',
        },
    ],
    paths: {
        ...auth,
        ...user,
        ...project,
    },
    components: {
        securitySchemes: {
            bearerAuth: {
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT',
                description: 'Insira o token Sanctum no formato: Bearer {token}. Em ambiente local, você também pode usar o MASTER_TOKEN (ex: taskflow-dev-master-token).',
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
            Project: {
                type: 'object',
                properties: {
                    id: { type: 'integer', example: 1 },
                    name: { type: 'string', maxLength: 150, example: 'Sistema de Gestão' },
                    active: { type: 'boolean', example: true },
                    created_at: { type: 'string', format: 'date-time', example: '2026-09-29T16:00:00.000000Z' },
                    updated_at: { type: 'string', format: 'date-time', example: '2026-09-29T16:00:00.000000Z' },
                },
            },
            StoreProjectRequest: {
                type: 'object',
                required: ['name'],
                properties: {
                    name: { type: 'string', maxLength: 150, example: 'Sistema de Gestão' },
                    active: { type: 'boolean', default: true, example: true },
                },
            },
            UpdateProjectRequest: {
                type: 'object',
                properties: {
                    name: { type: 'string', maxLength: 150, example: 'Sistema de Gestão Atualizado' },
                    active: { type: 'boolean', example: false },
                },
            },
            ProjectSingleResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Projeto criado com sucesso.' },
                    data: { $ref: '#/components/schemas/Project' },
                },
            },
            ProjectListResponse: {
                type: 'object',
                properties: {
                    data: {
                        type: 'array',
                        items: { $ref: '#/components/schemas/Project' },
                    },
                    links: {
                        type: 'object',
                        properties: {
                            first: { type: 'string', nullable: true },
                            last: { type: 'string', nullable: true },
                            prev: { type: 'string', nullable: true },
                            next: { type: 'string', nullable: true },
                        },
                    },
                    meta: {
                        type: 'object',
                        properties: {
                            current_page: { type: 'integer' },
                            from: { type: 'integer', nullable: true },
                            last_page: { type: 'integer' },
                            path: { type: 'string' },
                            per_page: { type: 'integer' },
                            to: { type: 'integer', nullable: true },
                            total: { type: 'integer' },
                        },
                    },
                },
            },
        },
    },
};
