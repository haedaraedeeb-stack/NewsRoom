<?php

use App\Jobs\NotifySubscribersJob;
use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

/**
 * Use the base TestCase (instead of plain Pest) so the Laravel
 * application container is bootstrapped. This is required because
 * Article::loadMissing() inside the job constructor needs an active
 * database connection resolver, even though no actual queries run
 * since all relations are set manually as in-memory collections.
 */
uses(TestCase::class);

/**
 * Unit test for NotifySubscribersJob.
 *
 * Ensures that when the job is constructed with an Article instance,
 * the article and its required relations (user, attachments, comments, tags)
 * are properly loaded and accessible — without touching the database.
 *
 * This guarantees the job has all the data it needs once it is
 * serialized, queued, and later executed (e.g. to send the
 * ArticlePublishedMail to the correct writer).
 */
it('build job with article and its relations loaded', function () {
    // Arrange: build an Article with an in-memory 'user' relation
    // and empty collections for the remaining relations.
    $writer = new User(['name' => 'Haedra Deeb', 'email' => 'haedra@example.com']);
    $article = new Article(['title' => 'Laravel Backend Developer']);
    $article->setRelation('user', $writer);
    $article->setRelation('attachments', collect());
    $article->setRelation('comments', collect());
    $article->setRelation('tags', collect());

    // Act: instantiate the job with the prepared article.
    $job = new NotifySubscribersJob($article);

    // Assert: the job correctly holds the article instance,
    // the 'user' relation is loaded, and the writer's email is correct.
    expect($job->article)->toBeInstanceOf(Article::class)
        ->and($job->article->relationLoaded('user'))->toBeTrue()
        ->and($job->article->user->email)->toBe('haedra@example.com');
});
