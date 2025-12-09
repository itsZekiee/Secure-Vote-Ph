<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'organization_id',
        'role',
        'is_active',
        'profile_photo',
        'phone',
        'position',
        'department'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // User role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_ELECTION_OFFICER = 'election_officer';
    const ROLE_VOTER = 'voter';
    const ROLE_CANDIDATE = 'candidate';

    /**
     * Get the organization that owns the user
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the organizations created by this user
     */
    public function createdOrganizations()
    {
        return $this->hasMany(Organization::class, 'created_by');
    }

    /**
     * Get the elections created by this user
     */
    public function createdElections()
    {
        return $this->hasMany(Election::class, 'created_by');
    }

    /**
     * Get the partylists created by this user
     */
    public function createdPartylists()
    {
        return $this->hasMany(Partylist::class, 'created_by');
    }

    /**
     * Get the candidates created by this user
     */
    public function createdCandidates()
    {
        return $this->hasMany(Candidate::class, 'created_by');
    }

    /**
     * Get elections where this user is assigned as sub-admin
     */
    public function assignedElections()
    {
        return $this->belongsToMany(Election::class, 'election_user');
    }

    /**
     * Get the candidacies for this user (if user represents a candidate)
     */
    public function candidacies()
    {
        return $this->hasMany(Candidate::class, 'user_id');
    }

    /**
     * Get the votes cast by this user
     */
    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    /**
     * Scope query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query to filter by role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope query to filter by organization
     */
    public function scopeInOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is an election officer
     */
    public function isElectionOfficer()
    {
        return $this->hasRole(self::ROLE_ELECTION_OFFICER);
    }

    /**
     * Check if user is a voter
     */
    public function isVoter()
    {
        return $this->hasRole(self::ROLE_VOTER);
    }

    /**
     * Check if user is a candidate
     */
    public function isCandidate()
    {
        return $this->hasRole(self::ROLE_CANDIDATE);
    }

    /**
     * Check if user can manage elections
     */
    public function canManageElections()
    {
        return $this->isAdmin() || $this->isElectionOfficer();
    }

    /**
     * Get user's full name with role
     */
    public function getFullNameWithRoleAttribute()
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }

    /**
     * Get user's initials for avatar
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get user's profile photo URL or default avatar
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        // Return default avatar or generate one based on initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=ffffff&background=3b82f6';
    }

    /**
     * Check if user has voted in a specific election
     */
    public function hasVotedInElection($electionId)
    {
        return $this->votes()->where('election_id', $electionId)->exists();
    }

    /**
     * Get user's organization name
     */
    public function getOrganizationNameAttribute()
    {
        return $this->organization ? $this->organization->name : 'No Organization';
    }

    /**
     * Check if user belongs to the same organization as another user
     */
    public function isSameOrganization(User $user)
    {
        return $this->organization_id === $user->organization_id;
    }

    /**
     * Get all available roles
     */
    public static function getAvailableRoles()
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_ELECTION_OFFICER => 'Election Officer',
            self::ROLE_VOTER => 'Voter',
            self::ROLE_CANDIDATE => 'Candidate'
        ];
    }

    /**
     * Bootstrap the model
     */
    protected static function boot()
    {
        parent::boot();

        // Set default role when creating user
        static::creating(function ($user) {
            if (!$user->role) {
                $user->role = self::ROLE_VOTER;
            }

            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
        });
    }
}
