<?php

namespace App\Jobs;

use App\Party;

use App\Mail\ParticipantInvited;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InvitePartyParticipants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** The given party */
    protected $party;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Party $party)
    {
        $this->party = $party;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->party->participants as $participant) {
            Mail::to($participant->email)
                ->send(new ParticipantInvited($participant, $this->party));

            $participant->invited_at = now();
            $participant->save();
        }
    }
}
