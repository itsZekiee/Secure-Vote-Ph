<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'voter_id',
        'position_id',
        'latitude',
        'longitude',
        'ip_address',
        'user_agent',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    /**
     * The election this vote belongs to.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class, 'election_id', 'id');
    }

    /**
     * The candidate this vote belongs to.
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'id');
    }

    /**
     * The voter (user) who cast this vote.
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class, 'voter_id', 'id');
    }
}
