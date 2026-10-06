import auth from './docs/auth';
import project from './docs/project';
import statusTasks from './docs/status-tasks';
import subproject from './docs/subproject';
import task from './docs/task';
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
        {
            name: 'Subprojects',
            description: 'Endpoints de gerenciamento de subprojetos',
        },
        {
            name: 'StatusTasks',
            description: 'Endpoints de gerenciamento de status de tarefas',
        },
        {
            name: 'Tasks',
            description: 'Endpoints de gerenciamento de tarefas',
        },
    ],
    paths: {
        ...auth,
        ...user,
        ...project,
        ...subproject,
        ...statusTasks,
        ...task,
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
            Subproject: {
                type: 'object',
                properties: {
                    id: { type: 'integer', example: 1 },
                    project_id: { type: 'integer', example: 1 },
                    name: { type: 'string', example: 'Módulo Financeiro' },
                    active: { type: 'boolean', example: true },
                    project: { $ref: '#/components/schemas/Project' },
                    created_at: { type: 'string', format: 'date-time', example: '2026-10-06T16:00:00.000000Z' },
                    updated_at: { type: 'string', format: 'date-time', example: '2026-10-06T16:00:00.000000Z' },
                },
            },
            StoreSubprojectRequest: {
                type: 'object',
                required: ['project_id', 'name'],
                properties: {
                    project_id: { type: 'integer', example: 1 },
                    name: { type: 'string', maxLength: 150, example: 'Módulo Financeiro' },
                    active: { type: 'boolean', default: true, example: true },
                },
            },
            UpdateSubprojectRequest: {
                type: 'object',
                properties: {
                    project_id: { type: 'integer', example: 1 },
                    name: { type: 'string', maxLength: 150, example: 'Módulo Financeiro Atualizado' },
                    active: { type: 'boolean', example: false },
                },
            },
            SubprojectSingleResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Subprojeto criado com sucesso.' },
                    data: { $ref: '#/components/schemas/Subproject' },
                },
            },
            SubprojectListResponse: {
                type: 'object',
                properties: {
                    data: {
                        type: 'array',
                        items: { $ref: '#/components/schemas/Subproject' },
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
            Task: {
                type: 'object',
                properties: {
                    id: { type: 'integer', example: 1 },
                    code: { type: 'string', example: 'TASK-101', nullable: true },
                    name: { type: 'string', example: 'Desenvolver fluxo de autenticação' },
                    start_date: { type: 'string', format: 'date-time', example: '2026-10-06T19:00:00.000000Z' },
                    end_date: { type: 'string', format: 'date-time', example: null, nullable: true },
                    hours: { type: 'number', format: 'float', example: 4.5, nullable: true },
                    branch: { type: 'string', example: 'feature/auth-flow', nullable: true },
                    link: { type: 'string', example: 'https://github.com/org/repo/pull/12', nullable: true },
                    status_id: { type: 'integer', example: 1 },
                    status: { $ref: '#/components/schemas/StatusTask' },
                    subproject_id: { type: 'integer', example: 1, nullable: true },
                    subproject: { $ref: '#/components/schemas/Subproject' },
                    created_at: { type: 'string', format: 'date-time', example: '2026-10-06T19:00:00.000000Z' },
                    updated_at: { type: 'string', format: 'date-time', example: '2026-10-06T19:00:00.000000Z' },
                },
            },
            StoreTaskRequest: {
                type: 'object',
                required: ['name', 'status_id'],
                properties: {
                    code: { type: 'string', maxLength: 50, example: 'TASK-101' },
                    name: { type: 'string', maxLength: 255, example: 'Desenvolver fluxo de autenticação' },
                    start_date: { type: 'string', format: 'date-time', example: '2026-10-06T19:00:00.000000Z' },
                    end_date: { type: 'string', format: 'date-time', example: '2026-10-06T22:00:00.000000Z' },
                    hours: { type: 'number', format: 'float', example: 3.5 },
                    branch: { type: 'string', maxLength: 255, example: 'feature/auth' },
                    link: { type: 'string', maxLength: 500, example: 'https://github.com/org/repo/issues/10' },
                    status_id: { type: 'integer', example: 1 },
                    subproject_id: { type: 'integer', example: 1 },
                },
            },
            UpdateTaskRequest: {
                type: 'object',
                properties: {
                    code: { type: 'string', maxLength: 50, example: 'TASK-101' },
                    name: { type: 'string', maxLength: 255, example: 'Desenvolver fluxo atualizado' },
                    start_date: { type: 'string', format: 'date-time' },
                    end_date: { type: 'string', format: 'date-time' },
                    hours: { type: 'number', format: 'float', example: 6.0 },
                    branch: { type: 'string', maxLength: 255 },
                    link: { type: 'string', maxLength: 500 },
                    status_id: { type: 'integer', example: 2 },
                    subproject_id: { type: 'integer', example: 1 },
                },
            },
            TaskSingleResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Tarefa criada com sucesso.' },
                    data: { $ref: '#/components/schemas/Task' },
                },
            },
            TaskListResponse: {
                type: 'object',
                properties: {
                    data: {
                        type: 'array',
                        items: { $ref: '#/components/schemas/Task' },
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
            StatusTask: {
                type: 'object',
                properties: {
                    id: { type: 'integer', example: 1 },
                    slug: { type: 'string', example: 'em-andamento' },
                    name: { type: 'string', example: 'Em Andamento' },
                    active: { type: 'boolean', example: true },
                    created_at: { type: 'string', format: 'date-time', example: '2026-10-06T20:00:00.000000Z' },
                    updated_at: { type: 'string', format: 'date-time', example: '2026-10-06T20:00:00.000000Z' },
                },
            },
            StoreStatusTaskRequest: {
                type: 'object',
                required: ['name'],
                properties: {
                    name: { type: 'string', maxLength: 150, example: 'Em Andamento' },
                    slug: { type: 'string', maxLength: 100, example: 'em-andamento' },
                    active: { type: 'boolean', default: true, example: true },
                },
            },
            UpdateStatusTaskRequest: {
                type: 'object',
                properties: {
                    name: { type: 'string', maxLength: 150, example: 'Em Andamento Atualizado' },
                    slug: { type: 'string', maxLength: 100, example: 'em-andamento' },
                    active: { type: 'boolean', example: false },
                },
            },
            StatusTaskSingleResponse: {
                type: 'object',
                properties: {
                    message: { type: 'string', example: 'Status da tarefa criado com sucesso.' },
                    data: { $ref: '#/components/schemas/StatusTask' },
                },
            },
            StatusTaskListResponse: {
                type: 'object',
                properties: {
                    data: {
                        type: 'array',
                        items: { $ref: '#/components/schemas/StatusTask' },
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
