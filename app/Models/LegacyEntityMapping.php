<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LegacyEntityMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_system',
        'legacy_table',
        'legacy_id',
        'legacy_key',
        'legacy_key_hash',
        'target_type',
        'target_id',
        'mapping_status',
        'checksum',
        'last_imported_at',
    ];

    protected $casts = [
        'legacy_key' => 'array',
        'last_imported_at' => 'datetime',
    ];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  array<string, mixed>  $legacyKey
     */
    public static function makeLegacyKeyHash(array $legacyKey): string
    {
        ksort($legacyKey);

        return hash('sha256', json_encode($legacyKey, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
