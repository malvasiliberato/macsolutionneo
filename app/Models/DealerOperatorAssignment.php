<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DealerOperatorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_organization_id',
        'operator_membership_id',
        'assignment_role_code',
        'status',
        'is_primary',
        'assigned_at',
        'ended_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'assigned_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function dealerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'dealer_organization_id');
    }

    public function operatorMembership(): BelongsTo
    {
        return $this->belongsTo(OrganizationMembership::class, 'operator_membership_id');
    }

    public function legacyMappings(): MorphMany
    {
        return $this->morphMany(LegacyEntityMapping::class, 'target');
    }
}
