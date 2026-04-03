<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyMatchingHeuristics
{
    /**
     * @return array<int, string>
     */
    public function userSignals(): array;

    /**
     * @return array<int, string>
     */
    public function organizationSignals(): array;

    /**
     * @return array<int, string>
     */
    public function membershipSignals(): array;

    /**
     * @return array<int, string>
     */
    public function assignmentSignals(): array;
}
