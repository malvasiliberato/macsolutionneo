<?php

namespace App\Application\LegacyImport\Contracts;

use App\Application\LegacyImport\Support\LegacyRecordReference;

interface LegacyToTargetMapper
{
    /**
     * @param  array<string, mixed>  $legacyRow
     * @return array<string, mixed>
     */
    public function map(array $legacyRow, LegacyRecordReference $reference): array;
}
