<?php

namespace KillerQuote\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Areaseb\Core\Models\Company;

class SendQuote extends Mailable
{
    use Queueable, SerializesModels;

    public $filepath;
    public $company;
    public $object;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($filepath, Company $company, $object, $body)
    {
        $this->filepath = $filepath;
        $this->name = 'preventivo.pdf';
        $this->company = $company;
        $this->object = $object;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->company->email)
                    ->subject($this->object)
                    ->markdown('killerquote::email')
                    ->attach($this->filepath, [
                        'as' => $this->name,
                        'mime' => 'application/pdf',
                    ]);
    }
}
