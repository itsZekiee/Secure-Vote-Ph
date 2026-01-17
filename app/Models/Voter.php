<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Auditable;

class Voter extends Model
{
    use HasUuids, Auditable;
    protected $table = 'voters';

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'voter_id',
        'student_id',
        'password',
        'election_id',
        'user_id',
        'registration_status',
        'id_photo',
        'id_photo_hash',
        'failed_login_attempts',
        'locked_until',
        'is_permanently_blocked',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'is_permanently_blocked' => 'boolean',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
