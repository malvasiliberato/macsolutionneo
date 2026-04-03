<?php

namespace App\Application\LegacyImport\Support;

final class LegacyRecordReference
{
    /**
     * @param  array<string, mixed>  $key
     */
    public function __construct(
        public readonly string $source,
        public readonly string $table,
        public readonly array $key,
        public readonly ?string $legacyId = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'table' => $this->table,
            'key' => $this->key,
            'legacy_id' => $this->legacyId,
        ];
    }
}
