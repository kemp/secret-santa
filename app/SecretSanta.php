<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SecretSanta extends Model
{
    protected $fillable = ['party_id', 'from_id', 'to_id'];

    /** The given party of the secret santa */
    protected function party()
    {
        return $this->belongsTo(Party::class);
    }

    /** The creator in which the secret santa is from */
    protected function from()
    {
        return $this->belongsTo(Participant::class, 'from_id');
    }

    /** The creator in which the secret santa is to */
    protected function to()
    {
        return $this->belongsTo(Participant::class, 'to_id');
    }
}
