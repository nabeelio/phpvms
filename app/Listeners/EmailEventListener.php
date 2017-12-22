<?php
/**
 * Created by IntelliJ IDEA.
 * User: nabeelshahzad
 * Date: 12/22/17
 * Time: 11:44 AM
 */

namespace App\Listeners;

use Log;
use \App\Events\UserRegistered;


/**
 * Handle sending emails on different events
 * @package App\Listeners
 */
class EmailEventListener
{
    public function subscribe($events)
    {
        $events->listen(
            UserRegistered::class,
            'App\Listeners\EmailEventListener@onUserRegister'
        );
    }

    public function onUserRegister(UserRegistered $event)
    {
        Log::info($event->user->toArray());
    }

}
