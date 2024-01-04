<?php

namespace App\Listeners;

use App\Events\UserCreating as UserCreatingEvent;

class UserCreating
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreatingEvent $event): void
    {
        
    }
}
