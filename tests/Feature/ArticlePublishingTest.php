<?php

/**
 * @file ArticlePublishingTest.php
 * @description Feature test suite for Article Publishing & Side Effects.
 * Verifies that authorized writers can publish articles, triggering background
 * queues for confirmation emails and subscriber notifications. Also validates
 * negative scenarios ensuring no side effects occur upon failed/unauthorized requests.
 */

use App\Jobs\SendArticlePublishedNotificationJob;
use App\Mail\ArticlePublishedMail;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
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
 * Test: Verify that publishing an article successfully queues a confirmation email to the writer.
 */
it('returns 200 and queues an email to the writer when article is published', function() {
    Mail::fake();

    $user = User::factory()->create();
    $user->assignRole('writer');

    $article = Article::factory()->create([
        'user_id' => $user->id,
        'status'  => 'draft'
    ]);

    $response = $this->actingAs($user)->patchJson("/api/v1/articles/{$article->id}/publish");

    $response->assertStatus(200);

    Mail::assertSent(ArticlePublishedMail::class, function(ArticlePublishedMail $mail) use ($user){
        return $mail->hasTo($user->email);
    });
});

/**
 * Test: Verify that publishing an article successfully pushes the notification job to the queue.
 */
it('returns 200 and pushes a notification job to the queue when article is published', function() {
    Queue::fake();

    $user = User::factory()->create();
    $user->assignRole('writer');

    $article = Article::factory()->create([
        'user_id' => $user->id,
        'status'  => 'draft'
    ]);

    $response = $this->actingAs($user)->patchJson("/api/v1/articles/{$article->id}/publish");

    $response->assertStatus(200);

    Queue::assertPushed(SendArticlePublishedNotificationJob::class);
});

/**
 * Test: Verify that no email is queued if the publish action fails (e.g., unauthorized user).
 */
it('returns 403 and does not queue an email when an unauthorized user attempts to publish', function() {
    Mail::fake();

    $reader = User::factory()->create();
    $reader->assignRole('reader'); // Unauthorized role for publishing

    $writer = User::factory()->create();
    $article = Article::factory()->create([
        'user_id' => $writer->id,
        'status'  => 'draft'
    ]);

    $response = $this->actingAs($reader)->patchJson("/api/v1/articles/{$article->id}/publish");

    $response->assertStatus(403);

    Mail::assertNothingSent();
});

/**
 * Test: Verify that no job is pushed to the queue if the publish action fails.
 */
it('returns 403 and does not push a job to the queue when an unauthorized user attempts to publish', function() {
    Queue::fake();

    $reader = User::factory()->create();
    $reader->assignRole('reader'); // Unauthorized role for publishing

    $writer = User::factory()->create();
    $article = Article::factory()->create([
        'user_id' => $writer->id,
        'status'  => 'draft'
    ]);

    $response = $this->actingAs($reader)->patchJson("/api/v1/articles/{$article->id}/publish");
    $response->assertStatus(403);
    Queue::assertNothingPushed();
});
