<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $user;

    public function __construct(User $user, $subject = null)
    {
        $this->subject = $subject ?: 'A new user registered';
        $this->user = $user;
    }

    public function build()
    {
        return $this
            ->markdown('emails.admin.registered')
            ->subject($this->subject)
            ->with(['user' => $this->user]);
    }
}
