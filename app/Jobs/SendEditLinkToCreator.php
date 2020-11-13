<?php

namespace App\Jobs;

use App\Mail\CreatorEditLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEditLinkToCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $party;

    protected $participant;

    /**
     * SendEditLinkToCreator constructor.
     * @param $party
     * @param $participant
     */
    public function __construct($party, $participant)
    {
        $this->party = $party;
        $this->participant = $participant;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->participant->email)
            ->send(new CreatorEditLink($this->participant, $this->party));
    }
}
