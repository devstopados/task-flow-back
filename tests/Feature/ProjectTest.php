<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access projects endpoints', function () {
    $this->getJson(route('projects.index'))->assertStatus(401);
    $this->postJson(route('projects.store'), ['name' => 'Teste'])->assertStatus(401);
    $this->getJson(route('projects.show', 1))->assertStatus(401);
    $this->putJson(route('projects.update', 1), ['name' => 'Teste'])->assertStatus(401);
    $this->deleteJson(route('projects.destroy', 1))->assertStatus(401);
});

test('authenticated user can list projects with pagination', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 20; $i++) {
        Project::query()->create([
            'name' => "Projeto {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('projects.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'active', 'created_at', 'updated_at'],
            ],
            'links',
            'meta' => ['current_page', 'total', 'per_page'],
        ]);

    expect($response->json('data'))->toHaveCount(15);
});

test('authenticated user can list all projects using all parameter', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    for ($i = 1; $i <= 25; $i++) {
        Project::query()->create([
            'name' => "Projeto All {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('projects.index', ['all' => true]));

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(25);
});

test('authenticated user can filter projects by search and active status', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Project::query()->create(['name' => 'Sistema Financeiro', 'active' => true]);
    Project::query()->create(['name' => 'Sistema CRM', 'active' => false]);
    Project::query()->create(['name' => 'Aplicativo Mobile', 'active' => true]);

    $response = $this->getJson(route('projects.index', ['search' => 'Financeiro']));
    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Sistema Financeiro');

    $activeResponse = $this->getJson(route('projects.index', ['active' => 'true']));
    $activeResponse->assertStatus(200);
    expect($activeResponse->json('data'))->toHaveCount(2);

    $inactiveResponse = $this->getJson(route('projects.index', ['active' => 'false']));
    $inactiveResponse->assertStatus(200);
    expect($inactiveResponse->json('data'))->toHaveCount(1)
        ->and($inactiveResponse->json('data.0.name'))->toBe('Sistema CRM');
});

test('authenticated user can create a project successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Projeto Novo',
        'active' => true,
    ];

    $response = $this->postJson(route('projects.store'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'name', 'active', 'created_at', 'updated_at'],
        ])
        ->assertJson([
            'message' => 'Projeto criado com sucesso.',
            'data' => [
                'name' => 'Projeto Novo',
                'active' => true,
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Projeto Novo',
        'active' => true,
    ]);
});

test('project active defaults to true when omitted on creation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('projects.store'), [
        'name' => 'Projeto Sem Status',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'Projeto Sem Status',
                'active' => true,
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'Projeto Sem Status',
        'active' => true,
    ]);
});

test('cannot create project with invalid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('projects.store'), [
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);

    $longNameResponse = $this->postJson(route('projects.store'), [
        'name' => str_repeat('a', 151),
    ]);

    $longNameResponse->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('authenticated user can view a project by id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create([
        'name' => 'Projeto Detalhado',
        'active' => true,
    ]);

    $response = $this->getJson(route('projects.show', $project->id));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $project->id,
                'name' => 'Projeto Detalhado',
                'active' => true,
            ],
        ]);
});

test('viewing a non-existent project returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson(route('projects.show', 9999));

    $response->assertStatus(404);
});

test('authenticated user can update a project', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create([
        'name' => 'Nome Antigo',
        'active' => true,
    ]);

    $payload = [
        'name' => 'Nome Atualizado',
        'active' => false,
    ];

    $response = $this->putJson(route('projects.update', $project->id), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Projeto atualizado com sucesso.',
            'data' => [
                'id' => $project->id,
                'name' => 'Nome Atualizado',
                'active' => false,
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Nome Atualizado',
        'active' => false,
    ]);
});

test('cannot update project with invalid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create([
        'name' => 'Projeto Teste',
        'active' => true,
    ]);

    $response = $this->putJson(route('projects.update', $project->id), [
        'name' => str_repeat('b', 151),
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('updating non-existent project returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->putJson(route('projects.update', 9999), [
        'name' => 'Inexistente',
    ]);

    $response->assertStatus(404);
});

test('authenticated user can delete a project', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create([
        'name' => 'Projeto Para Deletar',
        'active' => true,
    ]);

    $response = $this->deleteJson(route('projects.destroy', $project->id));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Projeto removido com sucesso.',
        ]);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('deleting a non-existent project returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('projects.destroy', 9999));

    $response->assertStatus(404);
});

test('master token can authenticate project endpoints in local environment', function () {
    config(['app.env' => 'local']);
    config(['auth.master_token' => 'taskflow-dev-master-token']);

    $response = $this->withToken('taskflow-dev-master-token')
        ->getJson(route('projects.index'));

    $response->assertStatus(200);
});
