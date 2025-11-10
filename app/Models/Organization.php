<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'description',
        'address',
        'contact_number',
        'email',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }

    public function partylists()
    {
        return $this->hasMany(Partylist::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Accessors
     * Example: $organization->logo_url
     */
    public function getLogoUrlAttribute()
    {
        if (! $this->logo) {
            return null;
        }

        // adjust path if you store logos elsewhere
        return asset('storage/organizations/' . $this->logo);
    }
}
