<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $user;

    public function __construct(User $user, $subject=null)
    {
        $this->subject = $subject ?: 'Welcome to '.config('app.name').'!';
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.user.registered')
                    ->subject($this->subject)
                    ->with(['user' => $this->user]);
    }
}
