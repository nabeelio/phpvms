<?php

namespace Tests;

use App\Events\UserRegistered;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class RegistrationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testRegistration()
    {
        Event::fake();
        Notification::fake();

        /** @var UserService $userSvc */
        $userSvc = app(UserService::class);

        setting('pilots.auto_accept', true);

        $attrs = factory(User::class)->make()->makeVisible(['api_key', 'name', 'email'])->toArray();
        $attrs['password'] = Hash::make('secret');
        $user = $userSvc->createUser($attrs);

        $this->assertEquals(UserState::ACTIVE, $user->state);

        Event::assertDispatched(UserRegistered::class, function ($e) use ($user) {
            return $e->user->id === $user->id
                && $e->user->state === $user->state;
        });

        /*Notification::assertSentTo(
            [$user],
            \App\Notifications\Messages\UserRegistered::class
        );*/
    }
}
