<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    // adjust as needed: $fillable or $guarded
    protected $guarded = [];

    /**
     * The user that represents this candidate (if applicable).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * The election this candidate belongs to.
     */
    public function election()
    {
        return $this->belongsTo(\App\Models\Election::class, 'election_id', 'id');
    }

    /**
     * The position this candidate is running for.
     */
    public function position()
    {
        return $this->belongsTo(\App\Models\Position::class, 'position_id', 'id');
    }

    /**
     * The partylist this candidate belongs to.
     */
    public function partylist()
    {
        return $this->belongsTo(\App\Models\Partylist::class, 'partylist_id', 'id');
    }

    /**
     * Votes cast for this candidate.
     *
     * Allows usage of ->withCount('votes') and ->votes()
     * Assumes a Vote model with a candidate_id foreign key.
     */
    public function votes()
    {
        return $this->hasMany(\App\Models\Vote::class, 'candidate_id', 'id');
    }
}
