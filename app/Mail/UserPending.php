<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserPending extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $user;

    public function __construct(User $user, $subject = null)
    {
        $this->subject = $subject ?: 'Your registration is pending!';
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.user.pending')
            ->subject($this->subject)
            ->with(['user' => $this->user]);
    }
}
