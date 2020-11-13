<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    /** Fillable attributes */
    protected $fillable = ['party_id', 'edit_token', 'name', 'email', 'wishlist'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'invitation_token';
    }

    /**
     * Fetch the associated party of the creator.
     * @returns
     */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
