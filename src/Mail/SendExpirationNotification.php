<?php

namespace KillerQuote\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class SendExpirationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $preventivi;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($preventivi)
    {
        $this->preventivi = $preventivi;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to(User::find(1))->subject('Notifica scadenze')->markdown('killerquote::notification');
    }
}
