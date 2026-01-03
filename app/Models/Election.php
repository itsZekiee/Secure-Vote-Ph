<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'registration_deadline',
        'status',
        'code',
        'created_by',
    ];


    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'require_geo_verification' => 'boolean',
        'geo_latitude' => 'decimal:8',
        'geo_longitude' => 'decimal:8',
        'geo_radius_meters' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this election
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get users assigned as sub-admins for this election
     */
    public function subAdmins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'election_user')->withTimestamps();
    }

    /**
     * Get the organization that owns this election
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all positions for this election
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class)->orderBy('order');
    }

    /**
     * Get all candidates for this election (through positions)
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Get all votes cast in this election
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get all partylists associated with this election
     */
    public function partylists(): HasMany
    {
        return $this->hasMany(Partylist::class);
    }

    /**
     * Scope to filter active elections
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter upcoming elections
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope to filter ongoing elections
     */
    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('status', 'active');
    }

    /**
     * Scope to filter completed elections
     */
    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now())
            ->orWhere('status', 'completed');
    }
}
