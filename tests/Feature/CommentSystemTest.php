<?php

/**
 * @file CommentSystemTest.php
 * @description Feature test suite for the Comment System in the NewsRoom project.
 * Verifies that readers can successfully add comments to published articles,
 * ensures proper database persistence with correct relationships, and validates
 * the notification dispatching logic (sending to authors, skipping the commenter).
 */

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    /**
     * Seed essential roles before each test execution to ensure proper authorization checks.
     */
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'writer', 'guard_name' => 'web']);
    Role::create(['name' => 'reader', 'guard_name' => 'web']);
});

/**
 * Test: Verify that a reader can successfully add a comment to an existing published article.
 * Expected HTTP status is 201 Created.
 */
it('return 201 if reader add comment in exist article', function () {
    $user = User::factory()->create();
    $user->assignRole('reader');
    $article = Article::factory()->create([
        'status' => 'published'
    ]);

    $response = $this->actingAs($user)->postJson("/api/articles/{$article->id}/comments", [
        'body' => 'Test comment to see if reader can add comment on article .'
    ]);

    $response->assertStatus(201);
});

/**
 * Test: Verify that the submitted comment is correctly saved in the database,
 * linked to the correct user (reader) and the correct commentable entity (article).
 */
it('return 201 for right saving comment in database', function () {
    $user = User::factory()->create();
    $user->assignRole('reader');
    $article = Article::factory()->create([
        'status' => 'published'
    ]);

    $response = $this->actingAs($user)->postJson("/api/articles/{$article->id}/comments", [
        'body' => 'Test comment to see if reader can add comment on article .'
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('comments', [
        'body'             => 'Test comment to see if reader can add comment on article .',
        'user_id'          => $user->id,
        'commentable_id'   => $article->id,
        'commentable_type' => Article::class
    ]);
});

/**
 * Test: Verify that a notification is dispatched to the article's author
 * immediately after a new comment is successfully added.
 */
it('return 201 for sending notification for writer', function () {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('reader');
    $article = Article::factory()->create([
        'status' => 'published'
    ]);

    $response = $this->actingAs($user)->postJson("/api/articles/{$article->id}/comments", [
        'body' => 'Test comment to see if reader can add comment on article .'
    ]);

    $response->assertStatus(201);

    Notification::assertSentTo([$article->user], NewCommentNotification::class);
});

/**
 * Test: Verify that the user who authored the comment (the reader) does not
 * receive a notification about their own comment.
 */
it('return 201 for not sending notification for reader who type comment', function () {
    Notification::fake();

    $user = User::factory()->create();
    $user->assignRole('reader');
    $article = Article::factory()->create([
        'status' => 'published'
    ]);

    $response = $this->actingAs($user)->postJson("/api/articles/{$article->id}/comments", [
        'body' => 'Test comment to see if reader can add comment on article .'
    ]);

    $response->assertStatus(201);

    Notification::assertNotSentTo([$user], NewCommentNotification::class);
});
