<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLoginDetails extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $user, $newpw;

    public function __construct(User $user, $newpw=null, $subject=null)
    {
        $this->subject = $subject ?: 'New Login Details';
        $this->newpw = $newpw ?: 'N/A';
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.user.new_login_details')
                    ->subject($this->subject)
                    ->with(['user' => $this->user, 'newpw' => $this->newpw]);
    }
}
