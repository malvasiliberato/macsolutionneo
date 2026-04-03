<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'parent_organization_id',
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_organization_id');
    }

    public function childOrganizations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_organization_id');
    }

    public function dealerOperatorAssignments(): HasMany
    {
        return $this->hasMany(DealerOperatorAssignment::class, 'dealer_organization_id');
    }

    public function productEnablements(): HasMany
    {
        return $this->hasMany(OrganizationProductEnablement::class);
    }

    public function enabledProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'organization_product_enablements')
            ->withPivot('status', 'source', 'enabled_at')
            ->withTimestamps();
    }

    public function legacyMappings(): MorphMany
    {
        return $this->morphMany(LegacyEntityMapping::class, 'target');
    }
}
