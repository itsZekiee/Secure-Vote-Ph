<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ArchivedVote extends Model
{
    use HasUuids;

    protected $fillable = [
        'archived_election_id',
        'original_vote_id',
        'candidate_id',
        'voter_id',
        'position_id',
        'ip_address',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function archivedElection()
    {
        return $this->belongsTo(ArchivedElection::class);
    }
}
