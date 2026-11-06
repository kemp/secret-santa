<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /** Answers only track when they were last changed. */
    const CREATED_AT = null;

    /** Fillable attributes */
    protected $fillable = ['participant_id', 'question_id', 'year', 'body'];

    /** Attribute casts */
    protected $casts = [
        'year' => 'integer',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
