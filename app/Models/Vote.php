<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'voter_id',
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
     *
     * Project has a `User` model (no `Voter` model present), so reference User.
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voter_id', 'id');
    }
}
