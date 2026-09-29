<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('user can register successfully', function () {
    $payload = [
        'name' => 'Ana Freitas',
        'email' => 'ana@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson(route('auth.register'), $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            'token',
        ])
        ->assertJson([
            'message' => 'User registered successfully.',
            'user' => [
                'name' => 'Ana Freitas',
                'email' => 'ana@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'ana@example.com',
        'name' => 'Ana Freitas',
    ]);
});

test('user cannot register with an existing email', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
    ]);

    $payload = [
        'name' => 'Another Ana',
        'email' => 'ana@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson(route('auth.register'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user cannot register without password confirmation', function () {
    $payload = [
        'name' => 'Ana Freitas',
        'email' => 'ana@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!',
    ];

    $response = $this->postJson(route('auth.register'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('user can login with valid credentials', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'Password123!',
    ]);

    $payload = [
        'email' => 'ana@example.com',
        'password' => 'Password123!',
    ];

    $response = $this->postJson(route('auth.login'), $payload);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            'token',
        ])
        ->assertJson([
            'message' => 'Login successful.',
            'user' => [
                'email' => 'ana@example.com',
            ],
        ]);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'Password123!',
    ]);

    $payload = [
        'email' => 'ana@example.com',
        'password' => 'WrongPassword',
    ];

    $response = $this->postJson(route('auth.login'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('authenticated user can view their profile via auth me', function () {
    $user = User::factory()->create([
        'name' => 'Ana Freitas',
        'email' => 'ana@example.com',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('auth.me'));

    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => 'Ana Freitas',
                'email' => 'ana@example.com',
            ],
        ]);
});

test('authenticated user can view their profile via user endpoint', function () {
    $user = User::factory()->create([
        'name' => 'Ana Freitas',
        'email' => 'ana@example.com',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson(route('user.profile'));

    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => 'Ana Freitas',
                'email' => 'ana@example.com',
            ],
        ]);
});

test('unauthenticated user cannot view profile', function () {
    $response = $this->getJson(route('auth.me'));

    $response->assertStatus(401);
});

test('authenticated user can logout and revoke token', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('auth.logout'));

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully.',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
