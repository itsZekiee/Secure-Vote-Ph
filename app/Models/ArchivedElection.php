<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ArchivedElection extends Model
{
    use HasUuids;

    protected $fillable = [
        'original_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'organization_id',
        'created_by',
        'settings',
        'results_summary',
        'archived_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'settings' => 'array',
        'results_summary' => 'array',
        'archived_at' => 'datetime',
    ];

    public function votes()
    {
        return $this->hasMany(ArchivedVote::class);
    }
}
