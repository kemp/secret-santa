<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    /** 
     * Fetch the participants in a given party
     * @return 
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Determine if all party participants are confirmed
     */
    public function allParticipantsConfirmed()
    {
        return $this->participants->reduce(function ($carry, $participant) {
            return $carry && $participant->confirmed_at;
        }, true);
    }
}
