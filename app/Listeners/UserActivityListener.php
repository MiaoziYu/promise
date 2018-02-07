<?php

namespace App\Listeners;

use App\Events\UserActed;
use App\UserActivity;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserActivityListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserActed  $event
     * @return void
     */
    public function handle(UserActed $event)
    {
        UserActivity::create([
            'subject_id' => $event->activity['subject_id'],
            'user_id' => $event->activity['user_id'],
            'subject_type' => $event->activity['subject_type'],
            'name' => $event->activity['name'],
            'value' => !empty($event->activity['value']) ? $event->activity['value'] : null,
        ]);
    }
}
