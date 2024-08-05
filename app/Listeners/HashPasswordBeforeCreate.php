<?php

namespace App\Listeners;

use App\Events\BeforeCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleBeforeCreate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     */
    public function handle(BeforeCreate $event): void
    {
        if (isset($event->data['password'])) {
            $event->data['password'] = bcrypt($event->data['password']);
        }
    }
}
