<?php

use App\Mail\ArticlePublishedMail;
use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

/**
 * Test Case: Verify ArticlePublishedMail configuration.
 * * This unit test ensures that the email object is correctly initialized
 * with the appropriate subject, payload data, and recipient information,
 * without relying on a database connection.
 * * @covers \App\Mail\ArticlePublishedMail
 */
it('has the correct subject and recipient', function () {
    // --- Arrange (Preparation) ---
    // Mocking the dependencies using plain PHP objects to avoid database dependency.
    $writer = new User([
        'name'  => 'Haedra Deeb',
        'email' => 'haedra@example.com',
    ]);

    $article = new Article(['title' => 'Laravel Backend Developer']);

    // Manually setting the relationship as we are not using database persistence (create).
    $article->setRelation('user', $writer);

    // --- Act (Execution) ---
    // Instantiate the Mailable object with the prepared article.
    $mailable = new ArticlePublishedMail($article);

    // --- Assert (Verification) ---

    // 1. Verify the email subject defined in the envelope.
    expect($mailable->envelope()->subject)
        ->toBe('Your Article Has Been Published!');

    // 2. Verify the data passed to the Markdown template.
    $data = $mailable->content()->with;
    expect($data['writerName'])->toBe('Haedra Deeb')
        ->and($data['articleTitle'])->toBe('Laravel Backend Developer');

    // 3. Verify that the Mailable is addressed to the correct recipient.
    $mailable->assertTo($writer->email);
});
