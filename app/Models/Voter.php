<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voter extends Model
{
    protected $table = 'voters';

    protected $fillable = [
        'election_id',
        'name',
        'email',
        'phone',
        'voter_id',
        'password',
        'election_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
