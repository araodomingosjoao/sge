<?php

namespace App\Listeners;

use App\Events\BeforeUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleBeforeUpdate
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
    public function handle(BeforeUpdate $event): void
    {
        if (isset($event->data['password'])) {
            $event->data['password'] = bcrypt($event->data['password']);
        }
    }
}
