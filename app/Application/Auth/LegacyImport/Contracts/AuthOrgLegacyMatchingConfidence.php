<?php

namespace App\Application\Auth\LegacyImport\Contracts;

interface AuthOrgLegacyMatchingConfidence
{
    /**
     * @return array<int, string>
     */
    public function levels(): array;

    /**
     * @return array<int, string>
     */
    public function strongSignals(): array;

    /**
     * @return array<int, string>
     */
    public function mediumSignals(): array;

    /**
     * @return array<int, string>
     */
    public function weakSignals(): array;

    /**
     * @return array<int, string>
     */
    public function downgradeConditions(): array;
}
