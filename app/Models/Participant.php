<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    /** Fillable attributes */
    protected $fillable = ['party_id', 'edit_token', 'name', 'email', 'wishlist'];

    /** Attributes that should never leak into arrays / JSON. */
    protected $hidden = ['edit_token', 'access_token'];

    /**
     * Give every participant a private link token when they join.
     */
    protected static function booted()
    {
        static::creating(function (Participant $participant) {
            $participant->access_token ??= Str::random(32);
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'access_token';
    }

    /**
     * Fetch the associated party of the creator.
     *
     * @returns
     */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * All of the participant's questionnaire answers, across years.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * The Secret Santa assignment where this participant is the Santa.
     */
    public function assignment()
    {
        return $this->hasOne(SecretSanta::class, 'from_id');
    }

    /**
     * The participant's answers for their party's year, keyed by question id.
     *
     * @return Collection<int, string|null>
     */
    public function answersForYear(int $year): Collection
    {
        return $this->answers()
            ->where('year', $year)
            ->pluck('body', 'question_id');
    }

    /**
     * The participant's answered questions for their party's year, in display
     * order, ready to show to their Secret Santa. Blank answers are skipped.
     *
     * @return Collection<int, array{question: Question, answer: string}>
     */
    public function profile(): Collection
    {
        $year = $this->party->year;
        $answers = $this->answersForYear($year);

        return Question::forYear($year)->get()
            ->filter(fn (Question $question) => filled($answers->get($question->id)))
            ->map(fn (Question $question) => [
                'question' => $question,
                'answer' => $answers->get($question->id),
            ])
            ->values();
    }
}
