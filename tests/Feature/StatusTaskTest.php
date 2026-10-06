<?php

use App\Models\StatusTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access status-tasks endpoints', function () {
    $this->getJson(route('status-tasks.index'))->assertStatus(401);
    $this->postJson(route('status-tasks.store'), ['name' => 'Teste'])->assertStatus(401);
    $this->getJson(route('status-tasks.show', 1))->assertStatus(401);
    $this->putJson(route('status-tasks.update', 1), ['name' => 'Teste'])->assertStatus(401);
    $this->deleteJson(route('status-tasks.destroy', 1))->assertStatus(401);
});

test('authenticated user can list status-tasks with pagination', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 20; $i++) {
        StatusTask::query()->create([
            'slug' => "status-{$i}",
            'name' => "Status {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('status-tasks.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'slug', 'name', 'active', 'created_at', 'updated_at'],
            ],
            'links',
            'meta' => ['current_page', 'total', 'per_page'],
        ]);

    expect($response->json('data'))->toHaveCount(10)
        ->and($response->json('meta.per_page'))->toBe(10);
});

test('authenticated user can list all status-tasks using all parameter', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 25; $i++) {
        StatusTask::query()->create([
            'slug' => "all-status-{$i}",
            'name' => "Status All {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('status-tasks.index', ['all' => true]));

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(25);
});

test('authenticated user can filter status-tasks by search and active', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    StatusTask::query()->create(['name' => 'Pendente', 'slug' => 'pendente', 'active' => true]);
    StatusTask::query()->create(['name' => 'Em Andamento', 'slug' => 'em-andamento', 'active' => false]);
    StatusTask::query()->create(['name' => 'Concluído', 'slug' => 'concluido', 'active' => true]);

    $searchResponse = $this->getJson(route('status-tasks.index', ['search' => 'Andamento']));
    $searchResponse->assertStatus(200);
    expect($searchResponse->json('data'))->toHaveCount(1)
        ->and($searchResponse->json('data.0.slug'))->toBe('em-andamento');

    $activeResponse = $this->getJson(route('status-tasks.index', ['active' => 'true']));
    $activeResponse->assertStatus(200);
    expect($activeResponse->json('data'))->toHaveCount(2);

    $inactiveResponse = $this->getJson(route('status-tasks.index', ['active' => 'false']));
    $inactiveResponse->assertStatus(200);
    expect($inactiveResponse->json('data'))->toHaveCount(1)
        ->and($inactiveResponse->json('data.0.slug'))->toBe('em-andamento');
});

test('authenticated user can create status-task with auto-generated slug', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('status-tasks.store'), [
        'name' => 'Em Teste',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Status da tarefa criado com sucesso.',
            'data' => [
                'name' => 'Em Teste',
                'slug' => 'em-teste',
                'active' => true,
            ],
        ]);

    $this->assertDatabaseHas('status_tasks', [
        'name' => 'Em Teste',
        'slug' => 'em-teste',
        'active' => true,
    ]);
});

test('authenticated user can create status-task with explicit slug and active status', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('status-tasks.store'), [
        'name' => 'Bloqueado',
        'slug' => 'custom-blocked',
        'active' => false,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Status da tarefa criado com sucesso.',
            'data' => [
                'name' => 'Bloqueado',
                'slug' => 'custom-blocked',
                'active' => false,
            ],
        ]);

    $this->assertDatabaseHas('status_tasks', [
        'name' => 'Bloqueado',
        'slug' => 'custom-blocked',
        'active' => false,
    ]);
});

test('cannot create status-task with invalid data or duplicate slug', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    StatusTask::query()->create(['name' => 'Existente', 'slug' => 'existente', 'active' => true]);

    $response = $this->postJson(route('status-tasks.store'), [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);

    $duplicateSlugResponse = $this->postJson(route('status-tasks.store'), [
        'name' => 'Outro Nome',
        'slug' => 'existente',
    ]);

    $duplicateSlugResponse->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('authenticated user can view status-task by id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $statusTask = StatusTask::query()->create(['name' => 'Aguardando Aprovação', 'slug' => 'aguardando-aprovacao', 'active' => true]);

    $response = $this->getJson(route('status-tasks.show', $statusTask->id));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $statusTask->id,
                'name' => 'Aguardando Aprovação',
                'slug' => 'aguardando-aprovacao',
                'active' => true,
            ],
        ]);
});

test('viewing a non-existent status-task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson(route('status-tasks.show', 9999));

    $response->assertStatus(404);
});

test('authenticated user can update status-task', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $statusTask = StatusTask::query()->create(['name' => 'Nome Antigo', 'slug' => 'nome-antigo', 'active' => true]);

    $response = $this->putJson(route('status-tasks.update', $statusTask->id), [
        'name' => 'Nome Novo',
        'slug' => 'nome-novo',
        'active' => false,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Status da tarefa atualizado com sucesso.',
            'data' => [
                'id' => $statusTask->id,
                'name' => 'Nome Novo',
                'slug' => 'nome-novo',
                'active' => false,
            ],
        ]);

    $this->assertDatabaseHas('status_tasks', [
        'id' => $statusTask->id,
        'name' => 'Nome Novo',
        'slug' => 'nome-novo',
        'active' => false,
    ]);
});

test('updating status-task allows keeping same slug', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $statusTask = StatusTask::query()->create(['name' => 'Teste Slug', 'slug' => 'teste-slug', 'active' => true]);

    $response = $this->putJson(route('status-tasks.update', $statusTask->id), [
        'name' => 'Teste Slug Atualizado',
        'slug' => 'teste-slug',
    ]);

    $response->assertStatus(200);
});

test('updating non-existent status-task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->putJson(route('status-tasks.update', 9999), [
        'name' => 'Inexistente',
    ]);

    $response->assertStatus(404);
});

test('authenticated user can delete status-task', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $statusTask = StatusTask::query()->create(['name' => 'Para Deletar', 'slug' => 'para-deletar', 'active' => true]);

    $response = $this->deleteJson(route('status-tasks.destroy', $statusTask->id));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Status da tarefa removido com sucesso.',
        ]);

    $this->assertDatabaseMissing('status_tasks', [
        'id' => $statusTask->id,
    ]);
});

test('deleting a non-existent status-task returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('status-tasks.destroy', 9999));

    $response->assertStatus(404);
});

test('master token can authenticate status-tasks endpoints in local environment', function () {
    config(['app.env' => 'local']);
    config(['auth.master_token' => 'taskflow-dev-master-token']);

    $response = $this->withToken('taskflow-dev-master-token')
        ->getJson(route('status-tasks.index'));

    $response->assertStatus(200);
});
