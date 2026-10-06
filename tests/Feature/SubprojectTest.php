<?php

use App\Models\Project;
use App\Models\Subproject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access subprojects endpoints', function () {
    $this->getJson(route('subprojects.index'))->assertStatus(401);
    $this->postJson(route('subprojects.store'), ['project_id' => 1, 'name' => 'Teste'])->assertStatus(401);
    $this->getJson(route('subprojects.show', 1))->assertStatus(401);
    $this->putJson(route('subprojects.update', 1), ['name' => 'Teste'])->assertStatus(401);
    $this->deleteJson(route('subprojects.destroy', 1))->assertStatus(401);
});

test('authenticated user can list subprojects with pagination', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Base', 'active' => true]);

    for ($i = 1; $i <= 20; $i++) {
        Subproject::query()->create([
            'project_id' => $project->id,
            'name' => "Subprojeto {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('subprojects.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'project_id', 'name', 'active', 'project', 'created_at', 'updated_at'],
            ],
            'links',
            'meta' => ['current_page', 'total', 'per_page'],
        ]);

    expect($response->json('data'))->toHaveCount(10)
        ->and($response->json('meta.per_page'))->toBe(10);
});

test('authenticated user can list all subprojects using all parameter', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Base', 'active' => true]);

    for ($i = 1; $i <= 25; $i++) {
        Subproject::query()->create([
            'project_id' => $project->id,
            'name' => "Subprojeto All {$i}",
            'active' => true,
        ]);
    }

    $response = $this->getJson(route('subprojects.index', ['all' => true]));

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(25);
});

test('authenticated user can filter subprojects by search, active status, and project_id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project1 = Project::query()->create(['name' => 'Projeto 1', 'active' => true]);
    $project2 = Project::query()->create(['name' => 'Projeto 2', 'active' => true]);

    Subproject::query()->create(['project_id' => $project1->id, 'name' => 'Módulo Financeiro', 'active' => true]);
    Subproject::query()->create(['project_id' => $project1->id, 'name' => 'Módulo RH', 'active' => false]);
    Subproject::query()->create(['project_id' => $project2->id, 'name' => 'Módulo Estoque', 'active' => true]);

    $response = $this->getJson(route('subprojects.index', ['search' => 'Financeiro']));
    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Módulo Financeiro');

    $activeResponse = $this->getJson(route('subprojects.index', ['active' => 'true']));
    $activeResponse->assertStatus(200);
    expect($activeResponse->json('data'))->toHaveCount(2);

    $projectFilterResponse = $this->getJson(route('subprojects.index', ['project_id' => $project2->id]));
    $projectFilterResponse->assertStatus(200);
    expect($projectFilterResponse->json('data'))->toHaveCount(1)
        ->and($projectFilterResponse->json('data.0.name'))->toBe('Módulo Estoque');
});

test('authenticated user can create a subproject successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);

    $payload = [
        'project_id' => $project->id,
        'name' => 'Subprojeto Novo',
        'active' => true,
    ];

    $response = $this->postJson(route('subprojects.store'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => ['id', 'project_id', 'name', 'active', 'project', 'created_at', 'updated_at'],
        ])
        ->assertJson([
            'message' => 'Subprojeto criado com sucesso.',
            'data' => [
                'project_id' => $project->id,
                'name' => 'Subprojeto Novo',
                'active' => true,
            ],
        ]);

    $this->assertDatabaseHas('subprojects', [
        'project_id' => $project->id,
        'name' => 'Subprojeto Novo',
        'active' => true,
    ]);
});

test('subproject active defaults to true when omitted on creation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);

    $response = $this->postJson(route('subprojects.store'), [
        'project_id' => $project->id,
        'name' => 'Subprojeto Sem Status',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'project_id' => $project->id,
                'name' => 'Subprojeto Sem Status',
                'active' => true,
            ],
        ]);

    $this->assertDatabaseHas('subprojects', [
        'name' => 'Subprojeto Sem Status',
        'active' => true,
    ]);
});

test('cannot create subproject with invalid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('subprojects.store'), [
        'project_id' => null,
        'name' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['project_id', 'name']);

    $nonExistentProjectResponse = $this->postJson(route('subprojects.store'), [
        'project_id' => 99999,
        'name' => 'Teste',
    ]);

    $nonExistentProjectResponse->assertStatus(422)
        ->assertJsonValidationErrors(['project_id']);

    $longNameResponse = $this->postJson(route('subprojects.store'), [
        'project_id' => 1,
        'name' => str_repeat('a', 151),
    ]);

    $longNameResponse->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('authenticated user can view a subproject by id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Subprojeto Detalhado',
        'active' => true,
    ]);

    $response = $this->getJson(route('subprojects.show', $subproject->id));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $subproject->id,
                'project_id' => $project->id,
                'name' => 'Subprojeto Detalhado',
                'active' => true,
            ],
        ]);
});

test('viewing a non-existent subproject returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson(route('subprojects.show', 9999));

    $response->assertStatus(404);
});

test('authenticated user can update a subproject', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Nome Antigo',
        'active' => true,
    ]);

    $payload = [
        'name' => 'Nome Atualizado',
        'active' => false,
    ];

    $response = $this->putJson(route('subprojects.update', $subproject->id), $payload);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Subprojeto atualizado com sucesso.',
            'data' => [
                'id' => $subproject->id,
                'name' => 'Nome Atualizado',
                'active' => false,
            ],
        ]);

    $this->assertDatabaseHas('subprojects', [
        'id' => $subproject->id,
        'name' => 'Nome Atualizado',
        'active' => false,
    ]);
});

test('cannot update subproject with invalid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Subprojeto Teste',
        'active' => true,
    ]);

    $response = $this->putJson(route('subprojects.update', $subproject->id), [
        'name' => str_repeat('b', 151),
        'project_id' => 99999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'project_id']);
});

test('updating non-existent subproject returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->putJson(route('subprojects.update', 9999), [
        'name' => 'Inexistente',
    ]);

    $response->assertStatus(404);
});

test('authenticated user can delete a subproject', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Pai', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Subprojeto Para Deletar',
        'active' => true,
    ]);

    $response = $this->deleteJson(route('subprojects.destroy', $subproject->id));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Subprojeto removido com sucesso.',
        ]);

    $this->assertDatabaseMissing('subprojects', [
        'id' => $subproject->id,
    ]);
});

test('deleting a non-existent subproject returns 404', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('subprojects.destroy', 9999));

    $response->assertStatus(404);
});

test('master token can authenticate subproject endpoints in local environment', function () {
    config(['app.env' => 'local']);
    config(['auth.master_token' => 'taskflow-dev-master-token']);

    $response = $this->withToken('taskflow-dev-master-token')
        ->getJson(route('subprojects.index'));

    $response->assertStatus(200);
});
