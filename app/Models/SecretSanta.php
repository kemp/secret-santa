<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretSanta extends Model
{
    protected $fillable = ['party_id', 'from_id', 'to_id'];

    /** The given party of the secret santa */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    /** The creator in which the secret santa is from */
    public function from()
    {
        return $this->belongsTo(Participant::class, 'from_id');
    }

    /** The creator in which the secret santa is to */
    public function to()
    {
        return $this->belongsTo(Participant::class, 'to_id');
    }
}
