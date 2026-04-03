<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationProductEnablement extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'product_id',
        'status',
        'source',
        'enabled_at',
    ];

    protected $casts = [
        'enabled_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
