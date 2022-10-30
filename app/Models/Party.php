<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $fillable = ['invitation_token', 'began_at'];


    /**
     * Fetch the participants in a given party
     * @return
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Determine the creatore of the party
     */
    public function creator()
    {
        return $this->participants()->first();
    }

    public function canBeInitiated()
    {
        return request()->query('edit_token') === $this->creator()->edit_token
            && $this->creator()->edit_token !== null
            && $this->participants()->count() > 2
            && $this->began_at === null;
    }
}
