<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyMatchingEdgeCases
{
    /**
     * @return array<int, string>
     */
    public function ambiguousSignals(): array;

    /**
     * @return array<int, string>
     */
    public function weakSignals(): array;

    /**
     * @return array<int, string>
     */
    public function stopConditions(): array;
}
