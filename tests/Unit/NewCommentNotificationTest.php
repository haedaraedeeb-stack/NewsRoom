<?php

use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Tests\TestCase;


uses(TestCase::class);

/**
 * Unit test for NewCommentNotification routing logic.
 *
 * Verifies that the via() method returns the correct delivery channel
 * based on the notifiable user's role, without touching the database.
 *
 * - Admin users  → notified via 'database'
 * - Writer users → notified via 'mail'
 */
it('routes notification via database for admin and via mail for writer', function () {
    // Comment instance is not used by via(), only passed to constructor.
    $comment = new Comment();

    // Mock an admin user: hasRole('admin') returns true.
    $admin = Mockery::mock(User::class);
    $admin->shouldReceive('hasRole')
        ->with('admin')
        ->andReturn(true);

    // Mock a writer user: hasRole('admin') returns false.
    $writer = Mockery::mock(User::class);
    $writer->shouldReceive('hasRole')
        ->with('admin')
        ->andReturn(false);

    $notification = new NewCommentNotification($comment);

    // Admin should be notified via the 'database' channel.
    expect($notification->via($admin))->toBe(['database']);

    // Writer should be notified via the 'mail' channel.
    expect($notification->via($writer))->toBe(['mail']);
});
