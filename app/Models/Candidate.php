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
        'first_name',      // Add this
        'middle_name',     // Add this
        'last_name',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function partylist(): BelongsTo
    {
        return $this->belongsTo(Partylist::class, 'partylist_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeForPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeForElection($query, $electionId)
    {
        return $query->where('election_id', $electionId);
    }

    public function scopeWithVoteCount($query)
    {
        return $query->withCount('votes');
    }
}
