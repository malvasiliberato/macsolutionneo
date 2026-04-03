<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyCandidateResolver
{
    /**
     * @param  array<string, mixed>  $legacyRow
     * @return array{
     *     candidate_type: string,
     *     resolution_status: string,
     *     resolution_reason: string
     * }
     */
    public function resolve(array $legacyRow): array;
}
