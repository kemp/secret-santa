<?php

namespace App\Mail;

use App\Models\SecretSanta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecretSantaInitiated extends Mailable
{
    use Queueable, SerializesModels;

    public $secretSanta;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SecretSanta $secretSanta)
    {
        $this->secretSanta = $secretSanta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Secret Santa is...')
            ->markdown('emails.secretsanta.initiated');
    }
}
