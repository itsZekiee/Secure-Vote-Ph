<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voter extends Model
{
    use HasUuids;
    protected $table = 'voters';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'voter_id',
        'student_id',
        'password',
        'election_id',
        'registration_status',
    ];

    protected $hidden = [
        'password',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
