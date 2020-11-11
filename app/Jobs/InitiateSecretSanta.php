<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SecretSantaInitiated;
use App\SecretSanta;
use Illuminate\Support\Facades\Mail;
use App\Party;

class InitiateSecretSanta implements ShouldQueue
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
        $participants = $this->party->participants->shuffle();
        $assignedParticipants = $participants->map(function($participant, $key) use ($participants) {
            return $participants[($key + 1) % $participants->count()];
        });

        // Loop through all of the participants.
        $participants->each(function ($participant, $key) use ($assignedParticipants) {

            // Assigned participant is just the next one in the array
            // or the first if they are last.
            $assignedParticipant = $assignedParticipants[$key];

            // Create the secret santa assigment.
            $secretSanta = SecretSanta::create([
                'party_id' => $participant->party->id,
                'from_id' => $participant->id,
                'to_id' => $assignedParticipant->id,
            ]);

            // Notify that participant of their secret santa.
            Mail::to($participant->email)
                ->queue(new SecretSantaInitiated($secretSanta));
        });
    }
}
