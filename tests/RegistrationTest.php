<?php

use App\Models\Enums\UserState;
use Illuminate\Support\Facades\Mail;

class RegistrationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @throws Exception
     *
     * @return void
     */
    public function testRegistration()
    {
        Event::fake();
        Mail::fake();

        $userSvc = app('App\Services\UserService');

        setting('pilots.auto_accept', true);

        $user = factory(App\Models\User::class)->create();
        $user = $userSvc->createPilot($user);

        $this->assertEquals(UserState::ACTIVE, $user->state);

        Event::assertDispatched(\App\Events\UserRegistered::class, function ($e) use ($user) {
            return $e->user->id === $user->id
                && $e->user->state === $user->state;
        });

        /*Mail::assertSent(\App\Mail\UserRegistered::class, function ($mail) use ($user) {
            return $mail->user->id === $user->id
                   && $mail->user->state === $user->state;
        });*/
    }
}
