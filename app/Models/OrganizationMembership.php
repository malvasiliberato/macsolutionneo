<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OrganizationMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'role_code',
        'status',
        'is_primary',
        'joined_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function dealerAssignments(): HasMany
    {
        return $this->hasMany(DealerOperatorAssignment::class, 'operator_membership_id');
    }

    public function legacyMappings(): MorphMany
    {
        return $this->morphMany(LegacyEntityMapping::class, 'target');
    }
}
