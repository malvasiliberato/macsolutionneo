<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyDatasetAdapter
{
    /**
     * @return iterable<int, array{
     *     legacy_table: string,
     *     candidate: string,
     *     status: string
     * }>
     */
    public function fetch(string $sourceSystem, string $legacyTable, int $batch): iterable;

    public function supports(string $dataset): bool;
}
