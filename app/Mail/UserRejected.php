<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRejected extends Mailable
{
    use Queueable, SerializesModels;

    private $subject,
            $user;

    public function __construct(User $user, $subject=null)
    {
        $this->subject = $subject ?: 'Your registration has been denied';
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.user.rejected')
                    ->subject($this->subject)
                    ->with(['user' => $this->user]);
    }
}
