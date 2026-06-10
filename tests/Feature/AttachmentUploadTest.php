<?php

/**
 * @file AttachmentUploadTest.php
 * @description Feature test suite for Article Attachment handling.
 * Verifies the ability of authorized writers to upload files, ensures proper
 * physical storage on the disk, and validates the creation of polymorphic
 * database records linking attachments to articles.
 */

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Database\Factories\AttachmentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    /**
     * Seed essential system roles before each test execution.
     * This ensures that role-based authorization checks behave correctly
     * without relying on a global database seeder.
     */
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
    Role::create(['name' => 'writer', 'guard_name' => 'web']);
    Role::create(['name' => 'reader', 'guard_name' => 'web']);
});

/**
 * Test: Uploading an attachment to an independently existing article.
 * * Scenario:
 * - A writer uploads a file to a specific article endpoint.
 * - Asserts an HTTP 200 (OK) response.
 * - Retrieves the newly created attachment record to dynamically verify its file path.
 * - Asserts the file physically exists on the mocked public disk.
 * - Asserts the polymorphic relationship is correctly saved in the database.
 */
it('returns 200 and stores the file when uploading an attachment to an existing article', function() {
    // 1. Mock the public disk to prevent storing test files on the actual filesystem
    Storage::fake('public');

    // 2. Setup: Create an authorized writer and a published article
    $writer = User::factory()->create();
    $writer->assignRole('writer');

    $article = Article::factory()->create([
        'user_id' => $writer->id,
        'status'  => 'published',
    ]);

    // 3. Create a fake image file for the upload payload
    $file = UploadedFile::fake()->image('image.jpg');

    // 4. Execution: Send POST request to the dedicated attachment endpoint
    $response = $this->actingAs($writer)->postJson("/api/v1/articles/{$article->id}/attachment", [
        'attachment' => $file,
    ]);

    // 5. Assertions: Response status
    $response->assertStatus(200);

    // Fetch the stored attachment from the DB to dynamically assert its storage path
    $attachment = Attachment::where('attachable_id', $article->id)
        ->where('attachable_type', Article::class)
        ->first();

    // Assert physical file existence using the stored path
    Storage::disk('public')->assertExists($attachment->file_path);

    // Assert database integrity for the polymorphic relationship
    $this->assertDatabaseHas('attachments', [
        'attachable_id'   => $article->id,
        'attachable_type' => Article::class,
    ]);
});

/**
 * Test: Uploading an attachment concurrently while creating a new article.
 * * Scenario:
 * - A writer submits a new article payload that includes an attachment file.
 * - Asserts an HTTP 201 (Created) response.
 * - Asserts the file is stored in the 'attachments' directory using its hash name.
 * - Asserts the newly created article is properly linked to the new attachment record.
 */
it('returns 201 and stores the file when writer creates an article with an attachment', function () {
    // 1. Mock the storage disk
    Storage::fake('public');

    // 2. Setup: Create an authorized writer
    $writer = User::factory()->create();
    $writer->assignRole('writer');

    // 3. Create a fake file
    $file = UploadedFile::fake()->image('image.jpg');

    // 4. Execution: Send POST request to create an article along with the file
    $response = $this->actingAs($writer)->postJson('/api/v1/articles', [
        'title'       => 'Laravel Backend Developer',
        'description' => 'Hi we are a small company we will make for you any kind of websites that you need also we will give you a bonus for each 2 website . be happy',
        'attachment'  => $file,
    ]);

    // 5. Assertions: Expected 201 Created status
    $response->assertStatus(201);

    // Verify file is saved in the target directory
    Storage::disk('public')->assertExists('attachments/' . $file->hashName());

    // Retrieve the latest created article to verify its associations
    $article = Article::latest()->first();

    // Verify the polymorphic attachment record exists and belongs to the new article
    $this->assertDatabaseHas('attachments', [
        'attachable_id'   => $article->id,
        'attachable_type' => Article::class,
    ]);
});
