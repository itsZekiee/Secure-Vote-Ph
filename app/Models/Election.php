<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'organization_id',
        'description',
        'voting_start',
        'voting_end',
        'enable_geo_location',
        'geo_latitude',
        'geo_longitude',
        'geo_radius',
        'status',
    ];

    protected $casts = [
        'voting_start' => 'datetime',
        'voting_end' => 'datetime',
        'enable_geo_location' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class)->orderBy('order');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
