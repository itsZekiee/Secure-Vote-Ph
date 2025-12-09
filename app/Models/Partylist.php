<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Partylist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'acronym',
        'description',
        'platform',
        'logo',
        'color',
        'organization_id',
        'election_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Get the user who created this partylist
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationships
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function members()
    {
        return $this->hasMany(PartylistMember::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->acronym ? "{$this->name} ({$this->acronym})" : $this->name;
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // Helper to safely delete logo file when needed
    public function deleteLogoFile(): void
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            Storage::disk('public')->delete($this->logo);
        }
    }
}
