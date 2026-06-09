<?php

/**
 * @file ArticleManagementTest.php
 * @description Feature test suite for Article Management endpoints.
 * Verifies authentication, role-based access control, validation,
 * and database interactions (creation and soft deletion).
 */

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    /**
     * Seed essential roles before each test.
     */
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'writer', 'guard_name' => 'web']);
    Role::create(['name' => 'reader', 'guard_name' => 'web']);
});

/**
 * Test: Ensure unauthenticated users cannot access the articles list.
 */
it('return 401 if user unauthorized', function () {
    $response = $this->getJson('/api/v1/articles');
    $response->assertStatus(401);
});

/**
 * Test: Ensure authenticated users can successfully access the articles list.
 */
it('return 200 if user authorized', function(){
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('/api/v1/articles');
    $response->assertStatus(200);
});

/**
 * Test: Ensure a user with the 'writer' role can create a new article.
 */
it('return 201 if user can add article', function(){
    $user = User::factory()->create();
    $user->assignRole('writer');
    $response = $this->actingAs($user)->postJson('/api/v1/articles',
        [
            'title' => 'Laravel Backend Developer',
            'description' => 'Hi we are a small company we will make for
            you any kind of websites that you need also we will give you
            a bonus for each 2 website . be happy',
        ]);
    $response->assertStatus(201);
});

/**
 * Test: Ensure a user with the 'reader' role is forbidden from creating an article.
 */
it('return 403 if user can not add article', function(){
    $user = User::factory()->create();
    $user->assignRole('reader');
    $response = $this->actingAs($user)->postJson('/api/v1/articles',
        [
            'title' => 'Laravel Backend Developer',
            'description' => 'Hi we are a small company we will make for
            you any kind of websites that you need also we will give you
            a bonus for each 2 website . be happy',
        ]);
    $response->assertStatus(403);
});

/**
 * Test: Validate that 'title' and 'description' fields are required.
 */
it('returns 422 if title or description is missing', function(){
    $user = User::factory()->create();
    $user->assignRole('writer');
    $response = $this->actingAs($user)->postJson('/api/v1/articles', []);
    $response->assertStatus(422)->assertJsonValidationErrors([
        'title'  => 'The article title is required.',
        'description' => 'The article content is required.',
    ]);
});

/**
 * Test: Verify that a successfully created article is persisted in the database.
 */
it('return 201 if writer create article and it saved in database', function (){
    $user = User::factory()->create([]);
    $user->assignRole('writer');

    $payload = [
        'title' => 'Laravel backend developer london, paris',
        'description' => 'Hi we are a small company we will make for
            you any kind of websites that you need also we will give you
            a bonus for each 2 website . be happy'
    ];

    $response = $this->actingAs($user)->postJson('/api/v1/articles', $payload);

    $response->assertStatus(201);
    $this->assertDatabaseHas('articles', [
        'title' => 'Laravel backend developer london, paris',
    ]);
});

/**
 * Test: Ensure that a user with the 'admin' role can soft-delete an article.
 */
it('return 200 if only admin can delete article', function(){
    $user = User::factory()->create();
    $user->assignRole('admin');
    $article = Article::factory()->create([]);
    $response = $this->actingAs($user)->deleteJson("/api/v1/articles/{$article->id}");
    $response->assertStatus(200);
    $this->assertSoftDeleted('articles', [
        'id' => $article->id,
    ]);
});

/**
 * Test: Ensure that non-admin users (e.g., writers) are forbidden from deleting articles.
 */
it('denies writers users from deleting an article', function () {
    $user = User::factory()->create();
    $user->assignRole('writer');
    $article = Article::factory()->create();
    $response = $this->actingAs($user)->deleteJson("/api/v1/articles/{$article->id}");
    $response->assertStatus(403);
    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'deleted_at' => null,
    ]);
});

/**
 * Test: Ensure that non-admin users (e.g., readers) are forbidden from deleting articles.
 */
it('denies readers users from deleting an article', function () {
    $user = User::factory()->create();
    $user->assignRole('reader');
    $article = Article::factory()->create();
    $response = $this->actingAs($user)->deleteJson("/api/v1/articles/{$article->id}");
    $response->assertStatus(403);
    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'deleted_at' => null,
    ]);
});
