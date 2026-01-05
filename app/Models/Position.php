<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'title',
        'description',
        'max_selection',
        'order',
    ];

    protected $casts = [
        'max_selection' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the election that owns this position
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Get all candidates for this position
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->orderBy('name');
    }

    /**
     * Get all votes cast for this position
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Scope to order positions by their order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to get positions with their candidates
     */
    public function scopeWithCandidates($query)
    {
        return $query->with('candidates');
    }
}
