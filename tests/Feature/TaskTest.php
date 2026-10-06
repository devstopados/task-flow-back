<?php

use App\Models\Project;
use App\Models\Subproject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access tasks endpoints', function () {
    $this->getJson(route('tasks.index'))->assertStatus(401);
    $this->postJson(route('tasks.store'), ['name' => 'Teste', 'status_id' => 1])->assertStatus(401);
    $this->getJson(route('tasks.show', 1))->assertStatus(401);
    $this->putJson(route('tasks.update', 1), ['name' => 'Teste'])->assertStatus(401);
    $this->deleteJson(route('tasks.destroy', 1))->assertStatus(401);
});

test('authenticated user can list tasks with pagination', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 20; $i++) {
        Task::query()->create([
            'code' => "TASK-{$i}",
            'name' => "Tarefa {$i}",
            'status_id' => 1,
        ]);
    }

    $response = $this->getJson(route('tasks.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'name',
                    'start_date',
                    'end_date',
                    'hours',
                    'branch',
                    'link',
                    'status_id',
                    'subproject_id',
                    'subproject',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta' => ['current_page', 'total', 'per_page'],
        ]);

    expect($response->json('data'))->toHaveCount(10)
        ->and($response->json('meta.per_page'))->toBe(10);
});

test('authenticated user can list all tasks using all parameter', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 25; $i++) {
        Task::query()->create([
            'code' => "ALL-{$i}",
            'name' => "Tarefa All {$i}",
            'status_id' => 1,
        ]);
    }

    $response = $this->getJson(route('tasks.index', ['all' => true]));

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(25);
});

test('authenticated user can filter tasks by search, subproject_id, and status_id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject1 = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Sub 1', 'active' => true]);
    $subproject2 = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Sub 2', 'active' => true]);

    Task::query()->create([
        'code' => 'AUTH-01',
        'name' => 'Tela de Login',
        'status_id' => 1,
        'subproject_id' => $subproject1->id,
    ]);

    Task::query()->create([
        'code' => 'DASH-01',
        'name' => 'Dashboard Financeiro',
        'status_id' => 2,
        'subproject_id' => $subproject1->id,
    ]);

    Task::query()->create([
        'code' => 'REP-01',
        'name' => 'Relatório Mensal',
        'status_id' => 1,
        'subproject_id' => $subproject2->id,
    ]);

    $searchResponse = $this->getJson(route('tasks.index', ['search' => 'Login']));
    $searchResponse->assertStatus(200);
    expect($searchResponse->json('data'))->toHaveCount(1)
        ->and($searchResponse->json('data.0.code'))->toBe('AUTH-01');

    $codeSearchResponse = $this->getJson(route('tasks.index', ['search' => 'DASH']));
    $codeSearchResponse->assertStatus(200);
    expect($codeSearchResponse->json('data'))->toHaveCount(1)
        ->and($codeSearchResponse->json('data.0.name'))->toBe('Dashboard Financeiro');

    $statusResponse = $this->getJson(route('tasks.index', ['status_id' => 2]));
    $statusResponse->assertStatus(200);
    expect($statusResponse->json('data'))->toHaveCount(1)
        ->and($statusResponse->json('data.0.code'))->toBe('DASH-01');

    $subprojectResponse = $this->getJson(route('tasks.index', ['subproject_id' => $subproject2->id]));
    $subprojectResponse->assertStatus(200);
    expect($subprojectResponse->json('data'))->toHaveCount(1)
        ->and($subprojectResponse->json('data.0.code'))->toBe('REP-01');
});

test('authenticated user can create a task successfully with all fields', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Sub 1', 'active' => true]);

    $payload = [
        'code' => 'TASK-500',
        'name' => 'Implementar Pagamento Stripe',
        'start_date' => '2026-10-06 10:00:00',
        'end_date' => '2026-10-06 18:00:00',
        'hours' => 8.00,
        'branch' => 'feature/stripe-payments',
        'link' => 'https://github.com/empresa/repo/pull/50',
        'status_id' => 1,
        'subproject_id' => $subproject->id,
    ];

    $response = $this->postJson(route('tasks.store'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'code',
                'name',
                'start_date',
                'end_date',
                'hours',
                'branch',
                'link',
                'status_id',
                'subproject_id',
                'subproject',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'message' => 'Tarefa criada com sucesso.',
            'data' => [
                'code' => 'TASK-500',
                'name' => 'Implementar Pagamento Stripe',
                'hours' => 8.0,
                'branch' => 'feature/stripe-payments',
                'link' => 'https://github.com/empresa/repo/pull/50',
                'status_id' => 1,
                'subproject_id' => $subproject->id,
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'code' => 'TASK-500',
        'name' => 'Implementar Pagamento Stripe',
        'status_id' => 1,
        'subproject_id' => $subproject->id,
    ]);
});

test('start_date defaults automatically when omitted on creation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => 'Tarefa Sem Data Manual',
        'status_id' => 1,
    ]);

    $response->assertStatus(201);
    expect($response->json('data.start_date'))->not->toBeNull();

    $this->assertDatabaseHas('tasks', [
        'name' => 'Tarefa Sem Data Manual',
        'status_id' => 1,
    ]);
});

test('cannot create task without required fields or invalid subproject', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('tasks.store'), [
        'name' => '',
        'status_id' => null,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'status_id']);

    $invalidSubprojectResponse = $this->postJson(route('tasks.store'), [
        'name' => 'Tarefa Teste',
        'status_id' => 1,
        'subproject_id' => 99999,
    ]);

    $invalidSubprojectResponse->assertStatus(422)
        ->assertJsonValidationErrors(['subproject_id']);
});

test('authenticated user can view a task by id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $task = Task::query()->create([
        'code' => 'VIEW-01',
        'name' => 'Tarefa Detalhada',
        'status_id' => 3,
    ]);

    $response = $this->getJson(route('tasks.show', $task->id));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $task->id,
                'code' => 'VIEW-01',
                'name' => 'Tarefa Detalhada',
                'status_id' => 3,
            ],
        ]);
});

test('viewing a non-existent task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson(route('tasks.show', 9999));

    $response->assertStatus(404);
});

test('authenticated user can update a task', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $task = Task::query()->create([
        'code' => 'UP-01',
        'name' => 'Nome Antigo',
        'status_id' => 1,
        'hours' => 2.0,
    ]);

    $payload = [
        'name' => 'Nome Atualizado',
        'status_id' => 2,
        'hours' => 5.5,
        'branch' => 'fix/login',
    ];

    $response = $this->putJson(route('tasks.update', $task->id), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tarefa atualizada com sucesso.',
            'data' => [
                'id' => $task->id,
                'name' => 'Nome Atualizado',
                'status_id' => 2,
                'hours' => 5.5,
                'branch' => 'fix/login',
            ],
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'name' => 'Nome Atualizado',
        'status_id' => 2,
    ]);
});

test('updating a non-existent task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->putJson(route('tasks.update', 9999), [
        'name' => 'Inexistente',
    ]);

    $response->assertStatus(404);
});

test('authenticated user can delete a task', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $task = Task::query()->create([
        'name' => 'Tarefa a Excluir',
        'status_id' => 1,
    ]);

    $response = $this->deleteJson(route('tasks.destroy', $task->id));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tarefa removida com sucesso.',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('deleting a non-existent task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('tasks.destroy', 9999));

    $response->assertStatus(404);
});

test('master token can authenticate task endpoints in local environment', function () {
    config(['app.env' => 'local']);
    config(['auth.master_token' => 'taskflow-dev-master-token']);

    $response = $this->withToken('taskflow-dev-master-token')
        ->getJson(route('tasks.index'));

    $response->assertStatus(200);
});
