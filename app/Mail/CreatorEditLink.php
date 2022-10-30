<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreatorEditLink extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;

    public $party;

    /**
     * CreatorEditLink constructor.
     *
     * @param $participant
     * @param $party
     */
    public function __construct($participant, $party)
    {
        $this->participant = $participant;
        $this->party = $party;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Here\'s the link for the secret santa')
            ->markdown('emails.creator.edit');
    }
}
