<?php

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Subproject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access sprints endpoints', function () {
    $this->getJson(route('sprints.index'))->assertStatus(401);
    $this->postJson(route('sprints.store'), [
        'name' => 'Sprint 1',
        'user_id' => 1,
        'subproject_id' => 1,
        'year' => 2026,
        'month' => 10,
    ])->assertStatus(401);
    $this->getJson(route('sprints.show', 1))->assertStatus(401);
    $this->putJson(route('sprints.update', 1), ['name' => 'Sprint 1'])->assertStatus(401);
    $this->deleteJson(route('sprints.destroy', 1))->assertStatus(401);
});

test('authenticated user can list sprints with pagination', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Base', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Subprojeto 1',
        'active' => true,
    ]);

    for ($i = 1; $i <= 15; $i++) {
        Sprint::query()->create([
            'user_id' => $user->id,
            'subproject_id' => $subproject->id,
            'name' => "Sprint {$i}",
            'year' => 2026,
            'month' => 10,
        ]);
    }

    $response = $this->getJson(route('sprints.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'year',
                    'month',
                    'user_id',
                    'user',
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

test('authenticated user can list all sprints using all parameter', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Base', 'active' => true]);
    $subproject = Subproject::query()->create([
        'project_id' => $project->id,
        'name' => 'Subprojeto 1',
        'active' => true,
    ]);

    for ($i = 1; $i <= 25; $i++) {
        Sprint::query()->create([
            'user_id' => $user->id,
            'subproject_id' => $subproject->id,
            'name' => "Sprint All {$i}",
            'year' => 2026,
            'month' => 10,
        ]);
    }

    $response = $this->getJson(route('sprints.index', ['all' => true]));

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(25);
});

test('authenticated user can filter sprints by search, user_id, subproject_id, year, and month', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    Sanctum::actingAs($user1);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject1 = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Alpha', 'active' => true]);
    $subproject2 = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Beta', 'active' => true]);

    Sprint::query()->create([
        'user_id' => $user1->id,
        'subproject_id' => $subproject1->id,
        'name' => 'Sprint Outubro Alpha',
        'year' => 2026,
        'month' => 10,
    ]);

    Sprint::query()->create([
        'user_id' => $user2->id,
        'subproject_id' => $subproject2->id,
        'name' => 'Sprint Novembro Beta',
        'year' => 2026,
        'month' => 11,
    ]);

    $searchResponse = $this->getJson(route('sprints.index', ['search' => 'Alpha']));
    $searchResponse->assertStatus(200);
    expect($searchResponse->json('data'))->toHaveCount(1)
        ->and($searchResponse->json('data.0.name'))->toBe('Sprint Outubro Alpha');

    $userFilterResponse = $this->getJson(route('sprints.index', ['user_id' => $user2->id]));
    $userFilterResponse->assertStatus(200);
    expect($userFilterResponse->json('data'))->toHaveCount(1)
        ->and($userFilterResponse->json('data.0.name'))->toBe('Sprint Novembro Beta');

    $subprojectFilterResponse = $this->getJson(route('sprints.index', ['subproject_id' => $subproject1->id]));
    $subprojectFilterResponse->assertStatus(200);
    expect($subprojectFilterResponse->json('data'))->toHaveCount(1);

    $monthFilterResponse = $this->getJson(route('sprints.index', ['month' => 11]));
    $monthFilterResponse->assertStatus(200);
    expect($monthFilterResponse->json('data'))->toHaveCount(1)
        ->and($monthFilterResponse->json('data.0.month'))->toBe(11);

    $anoMesFilterResponse = $this->getJson(route('sprints.index', ['ano' => 2026, 'mes' => 10]));
    $anoMesFilterResponse->assertStatus(200);
    expect($anoMesFilterResponse->json('data'))->toHaveCount(1);
});

test('authenticated user can create a sprint successfully', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Teste', 'active' => true]);

    $payload = [
        'user_id' => $user->id,
        'subproject_id' => $subproject->id,
        'name' => 'Sprint 1 - Autenticação',
        'year' => 2026,
        'month' => 10,
    ];

    $response = $this->postJson(route('sprints.store'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'year',
                'month',
                'user_id',
                'user',
                'subproject_id',
                'subproject',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJson([
            'message' => 'Sprint criada com sucesso.',
            'data' => [
                'name' => 'Sprint 1 - Autenticação',
                'year' => 2026,
                'month' => 10,
                'user_id' => $user->id,
                'subproject_id' => $subproject->id,
            ],
        ]);

    $this->assertDatabaseHas('sprints', [
        'name' => 'Sprint 1 - Autenticação',
        'year' => 2026,
        'month' => 10,
        'user_id' => $user->id,
        'subproject_id' => $subproject->id,
    ]);
});

test('authenticated user can create a sprint using ano and mes keys', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Teste', 'active' => true]);

    $payload = [
        'user_id' => $user->id,
        'subproject_id' => $subproject->id,
        'name' => 'Sprint 2 - Dashboard',
        'ano' => 2026,
        'mes' => 11,
    ];

    $response = $this->postJson(route('sprints.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Sprint criada com sucesso.',
            'data' => [
                'name' => 'Sprint 2 - Dashboard',
                'year' => 2026,
                'month' => 11,
            ],
        ]);

    $this->assertDatabaseHas('sprints', [
        'name' => 'Sprint 2 - Dashboard',
        'year' => 2026,
        'month' => 11,
    ]);
});

test('validation fails when creating sprint with invalid or missing data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson(route('sprints.store'), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'user_id', 'subproject_id', 'year', 'month']);

    $invalidResponse = $this->postJson(route('sprints.store'), [
        'name' => '',
        'user_id' => 999999,
        'subproject_id' => 999999,
        'year' => 1990,
        'month' => 13,
    ]);

    $invalidResponse->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'user_id', 'subproject_id', 'year', 'month']);
});

test('authenticated user can show sprint by id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Teste', 'active' => true]);

    $sprint = Sprint::query()->create([
        'user_id' => $user->id,
        'subproject_id' => $subproject->id,
        'name' => 'Sprint Detalhe',
        'year' => 2026,
        'month' => 10,
    ]);

    $response = $this->getJson(route('sprints.show', $sprint->id));

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $sprint->id,
                'name' => 'Sprint Detalhe',
                'year' => 2026,
                'month' => 10,
                'user_id' => $user->id,
                'subproject_id' => $subproject->id,
            ],
        ]);
});

test('show returns 404 when sprint not found', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson(route('sprints.show', 999999));

    $response->assertStatus(404);
});

test('authenticated user can update sprint', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    Sanctum::actingAs($user1);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Teste', 'active' => true]);

    $sprint = Sprint::query()->create([
        'user_id' => $user1->id,
        'subproject_id' => $subproject->id,
        'name' => 'Sprint Original',
        'year' => 2026,
        'month' => 10,
    ]);

    $response = $this->putJson(route('sprints.update', $sprint->id), [
        'name' => 'Sprint Atualizada',
        'user_id' => $user2->id,
        'year' => 2027,
        'month' => 12,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Sprint atualizada com sucesso.',
            'data' => [
                'id' => $sprint->id,
                'name' => 'Sprint Atualizada',
                'user_id' => $user2->id,
                'year' => 2027,
                'month' => 12,
            ],
        ]);

    $this->assertDatabaseHas('sprints', [
        'id' => $sprint->id,
        'name' => 'Sprint Atualizada',
        'user_id' => $user2->id,
        'year' => 2027,
        'month' => 12,
    ]);
});

test('authenticated user can delete sprint', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $project = Project::query()->create(['name' => 'Projeto Principal', 'active' => true]);
    $subproject = Subproject::query()->create(['project_id' => $project->id, 'name' => 'Subprojeto Teste', 'active' => true]);

    $sprint = Sprint::query()->create([
        'user_id' => $user->id,
        'subproject_id' => $subproject->id,
        'name' => 'Sprint Para Deletar',
        'year' => 2026,
        'month' => 10,
    ]);

    $response = $this->deleteJson(route('sprints.destroy', $sprint->id));

    $response->assertStatus(200)
        ->assertJson(['message' => 'Sprint removida com sucesso.']);

    $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
});

test('delete returns 404 when sprint not found', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->deleteJson(route('sprints.destroy', 999999));

    $response->assertStatus(404);
});
