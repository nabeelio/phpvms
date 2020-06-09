<?php

namespace Modules\Vacentral\Providers;

use App\Events\AcarsUpdate;
use App\Events\PirepAccepted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Vacentral\Listeners\AcarsUpdateListener;
use Modules\Vacentral\Listeners\PirepAcceptedEventListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AcarsUpdate::class   => [AcarsUpdateListener::class],
        PirepAccepted::class => [PirepAcceptedEventListener::class],
    ];
}
