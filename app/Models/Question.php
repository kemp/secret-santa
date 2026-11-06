<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /** Fillable attributes */
    protected $fillable = ['text', 'year', 'sort_order', 'is_active', 'options'];

    /** Attribute casts */
    protected $casts = [
        'year' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'options' => 'array',
    ];

    /**
     * The answers given to this question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Only questions that should be asked in the given year, in display order.
     */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('year')->orWhere('year', $year))
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Whether the question is answered by picking one of a fixed set of choices.
     */
    public function hasOptions(): bool
    {
        return ! empty($this->options);
    }

    /**
     * Whether the question is asked in the given year.
     */
    public function isAskedIn(int $year): bool
    {
        return $this->is_active && ($this->year === null || $this->year === $year);
    }
}
