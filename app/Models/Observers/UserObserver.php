<?php

namespace App\Models\Observers;

use App\Models\User;
use App\Services\UserService;

class UserObserver
{
    private $userSvc;

    public function __construct(UserService $userSvc)
    {
        $this->userSvc = $userSvc;
    }

    /**
     * After a user has been created, do some stuff
     *
     * @param User $user
     */
    public function created(User $user): void
    {
        $this->userSvc->findAndSetPilotId($user);
    }
}
