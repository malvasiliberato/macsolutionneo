<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyCandidateMatcher
{
    /**
     * @param  array<string, mixed>  $resolvedCandidate
     * @return array{
     *     match_status: string,
     *     match_reason: string
     * }
     */
    public function match(array $resolvedCandidate): array;
}
