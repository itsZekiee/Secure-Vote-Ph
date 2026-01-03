<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'access_link',
        'created_by',
        'organization_id',
        'require_geo_verification',
        'geo_latitude',
        'geo_longitude',
        'geo_radius_meters',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'require_geo_verification' => 'boolean',
        'geo_latitude' => 'decimal:8',
        'geo_longitude' => 'decimal:8',
        'geo_radius_meters' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($election) {
            if (empty($election->code)) {
                $election->code = self::generateUniqueCode();
            }
            $election->access_link = url("/voter/register/{$election->code}");
        });

        static::updating(function ($election) {
            if ($election->isDirty('code')) {
                $election->access_link = url("/voter/register/{$election->code}");
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subAdmins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'election_user')->withTimestamps();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function positions(): HasMany
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

    public function voters(): HasMany
    {
        return $this->hasMany(Voter::class);
    }

    public function partylists(): HasMany
    {
        return $this->hasMany(Partylist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now())
            ->orWhere('status', 'completed');
    }
}
