<?php

namespace App\Mail;

use App\Party;
use App\Participant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParticipantInvited extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;

    public $party;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant, Party $party)
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
        return $this->subject('You\'re invited to participate in Secret Santa!')
            ->markdown('emails.participant.invited');
    }
}
