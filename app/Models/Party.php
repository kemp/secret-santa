<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = ['invitation_token', 'began_at', 'year', 'deadline'];

    protected $casts = [
        'began_at' => 'datetime',
        'deadline' => 'datetime',
        'year' => 'integer',
    ];

    /**
     * Every party belongs to the year it was created in, unless told otherwise.
     */
    protected static function booted()
    {
        static::creating(function (Party $party) {
            $party->year ??= now()->year;
        });
    }

    /**
     * Fetch the participants in a given party
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function exclusions()
    {
        return $this->hasMany(Exclusion::class);
    }

    /**
     * Determine the creatore of the party
     */
    public function creator()
    {
        return $this->participants()->first();
    }

    /**
     * Whether the current request carries the creator's private edit token.
     */
    public function isManagedByRequest(): bool
    {
        $token = request()->query('edit_token');

        return is_string($token) && $token !== ''
            && hash_equals((string) $this->creator()?->edit_token, $token);
    }

    public function canBeEdited()
    {
        return request()->query('edit_token') === $this->creator()->edit_token
            && $this->began_at === null;
    }

    public function canBeInitiated()
    {
        return request()->query('edit_token') === $this->creator()->edit_token
            && $this->creator()->edit_token !== null
            && $this->participants()->count() > 2
            && $this->began_at === null;
    }

    /**
     * The questionnaire for this party's year.
     */
    public function questions()
    {
        return Question::forYear($this->year)->get();
    }

    /**
     * Participants may edit their answers until the deadline (if one is set).
     */
    public function acceptsAnswers(): bool
    {
        return $this->deadline === null || now()->lt($this->deadline);
    }
}
