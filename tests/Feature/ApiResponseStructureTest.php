<?php

/**
 * @file ApiResponseStructureTest.php
 * @description Feature tests verifying the standardized structure of API responses,
 * including pagination metadata and the secure serialization of sensitive data.
 */

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Test: Verify the paginated API response structure for articles.
 * * * Scenario:
 * - A user requests the list of their articles.
 * - The system should return a 200 OK status.
 * - The response JSON must strictly follow the standard paginated structure
 * containing 'data', 'links', and 'meta' keys.
 */
it('articles list returns data and meta structure', function () {
    // 1. Arrange: Create an authenticated user and 3 articles belonging specifically to them
    $user = User::factory()->create();
    Article::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    // 2. Act: Send a GET request to the articles index endpoint
    $response = $this->actingAs($user)->getJson('/api/v1/articles');

    // 3. Assert: Verify HTTP status and ensure the required JSON structure exists
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',    // The actual array of articles
            'meta',    // Pagination metadata (current_page, total, etc.)
            'links',   // Pagination navigation links
            'message', // Custom success message from the API resource
        ]);
});

/**
 * Test: Ensure sensitive data (like passwords) is stripped from the API response.
 * * * Scenario:
 * - A user requests the details of a specific article.
 * - The system retrieves the article along with its relationships (e.g., writer/user).
 * - The response JSON must explicitly exclude hidden attributes like user passwords
 * to prevent data leaks and maintain security.
 */
it('article details does not expose sensitive data', function () {
    // 1. Arrange: Create a user and an associated article
    $user = User::factory()->create();
    $article = Article::factory()->create([
        'user_id' => $user->id,
    ]);

    // 2. Act: Send a GET request to the specific article endpoint
    $response = $this->actingAs($user)->getJson("/api/v1/articles/{$article->id}");

    // 3. Assert: Verify HTTP status and check that passwords are not exposed anywhere
    $response->assertStatus(200)
        ->assertJsonMissing(['password']) // Ensures no password key exists at the root level
        ->assertJsonMissingPath('data.user.password') // Ensures no password key exists within nested relations
        ->assertJsonMissingPath('data.writer.password'); // Also checks the custom 'writer' key if mapped in the Resource
});
