<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exclusion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function from()
    {
        return $this->belongsTo(Participant::class);
    }

    public function to()
    {
        return $this->belongsTo(Participant::class);
    }
}
