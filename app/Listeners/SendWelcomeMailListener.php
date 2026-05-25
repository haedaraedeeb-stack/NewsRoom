<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Notifications\UserRegisterNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeMailListener implements ShouldQueue
{
    use InteractsWithQueue;
    public string $queue = 'notifications';
    public int $tries = 3;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredEvent $event): void
    {
        $event->user->notify(new UserRegisterNotification());
    }
}
