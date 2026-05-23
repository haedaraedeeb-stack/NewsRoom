<?php

namespace App\Notifications\Contracts;
    Interface NotificationInterface
    {
        public function send ($user, $message);
    }
