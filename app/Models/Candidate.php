<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'position_id',
        'user_id',
        'partylist_id',
        'name',
        'description',
        'photo',
        'party_affiliation',
        'created_by',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this candidate
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The user that represents this candidate (if applicable).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The election this candidate belongs to.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }

    /**
     * The position this candidate is running for.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * The partylist this candidate belongs to.
     */
    public function partylist(): BelongsTo
    {
        return $this->belongsTo(Partylist::class, 'partylist_id');
    }

    /**
     * Votes cast for this candidate.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }

    /**
     * Scope to order candidates by their order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to get candidates for a specific position
     */
    public function scopeForPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    /**
     * Scope to get candidates for a specific election
     */
    public function scopeForElection($query, $electionId)
    {
        return $query->where('election_id', $electionId);
    }

    /**
     * Scope to get candidates with vote counts
     */
    public function scopeWithVoteCount($query)
    {
        return $query->withCount('votes');
    }
}
