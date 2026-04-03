<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyDatasetAdapter;
use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateResolver;
use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyCandidateMatcher;
use Illuminate\Support\Collection;

final class AuthOrgLegacyDryRun
{
    public function __construct(
        private readonly MySqlAuthOrgLegacyDatasetAdapter $legacyDatasetAdapter,
        private readonly DefaultAuthOrgLegacyCandidateResolver $candidateResolver,
        private readonly DefaultAuthOrgLegacyCandidateMatcher $candidateMatcher,
        private readonly AuthOrgLegacyReconciliationReportBuilder $reportBuilder,
    ) {
    }

    /**
     * @return array{
     *     summary: array<string, scalar>,
     *     reconciliation: array<string, scalar|array<int, string>>
     * }
     */
    public function run(string $sourceSystem, string $legacyTable, string $dataset, int $batch, bool $dryRun): array
    {
        $resolvedRows = $this->inspect($sourceSystem, $legacyTable, $dataset, $batch);

        return $this->reportBuilder->build($resolvedRows, $sourceSystem, $legacyTable, $dataset, $batch, $dryRun);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function inspect(string $sourceSystem, string $legacyTable, string $dataset, int $batch): Collection
    {
        return collect($this->rows($sourceSystem, $legacyTable, $dataset, $batch))
            ->when($legacyTable !== 'all', fn ($collection) => $collection->where('legacy_table', $legacyTable))
            ->map(fn (array $row) => array_merge($row, $this->candidateResolver->resolve($row)))
            ->map(fn (array $row) => array_merge($row, $this->candidateMatcher->match($row)))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function classify(string $sourceSystem, string $legacyTable, string $dataset, int $batch): Collection
    {
        return $this->reportBuilder->classifyRows(
            $this->inspect($sourceSystem, $legacyTable, $dataset, $batch)
        );
    }

    /**
     * @return array<int, array{
     *     legacy_table: string,
     *     candidate: string,
     *     status: string
     * }>
     */
    private function rows(string $sourceSystem, string $legacyTable, string $dataset, int $batch): array
    {
        if ($this->legacyDatasetAdapter->supports($dataset)) {
            $rows = $this->legacyDatasetAdapter->fetch($sourceSystem, $legacyTable, $batch);

            return is_array($rows) ? $rows : iterator_to_array($rows);
        }

        return $this->dataset($dataset);
    }

    /**
     * @return array<int, array{
     *     legacy_table: string,
     *     candidate: string,
     *     status: string
     * }>
     */
    private function dataset(string $dataset): array
    {
        return match ($dataset) {
            'bootstrap-auth-org' => [
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => 'admin-bootstrap',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'admin-bootstrap'],
                    'legacy_email' => 'admin@macsolution.test',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'organization',
                    'status' => 'mapped_candidate',
                    'legacy_id' => 'macsolution-neo',
                    'legacy_key' => ['dealer_code' => 'macsolution-neo', 'kind' => 'organization'],
                    'legacy_code' => 'macsolution-neo',
                    'legacy_name' => 'Mac Solution Neo Workspace',
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'macsolution-neo', 'user_email' => 'admin@macsolution.test'],
                    'legacy_user_email' => 'admin@macsolution.test',
                    'legacy_organization_code' => 'macsolution-neo',
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'assignment',
                    'status' => 'mapped_candidate',
                    'legacy_key' => [
                        'dealer_code' => 'dealer-bootstrap',
                        'user_email' => 'admin@macsolution.test',
                        'assignment_role_code' => 'dealer_account_manager',
                    ],
                    'legacy_user_email' => 'admin@macsolution.test',
                    'legacy_dealer_code' => 'dealer-bootstrap',
                    'legacy_assignment_role_code' => 'dealer_account_manager',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '501',
                    'legacy_key' => ['dealer_id' => 2, 'collaborator_id' => 501],
                    'legacy_email' => 'seller@dealer-bootstrap.test',
                    'legacy_name' => 'Dealer Bootstrap Seller',
                    'legacy_username' => 'seller.bootstrap',
                    'legacy_role_code' => 'collaboratore',
                    'legacy_dealer_code' => 'dealer-bootstrap',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-bootstrap', 'user_email' => 'seller@dealer-bootstrap.test'],
                    'legacy_user_email' => 'seller@dealer-bootstrap.test',
                    'legacy_user_username' => 'seller.bootstrap',
                    'legacy_organization_code' => 'dealer-bootstrap',
                    'legacy_role_code' => 'collaboratore',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'collision',
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'membership',
                    'status' => 'unresolved',
                ],
            ],
            'bootstrap-auth-org-signal-hardening' => [
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '701',
                    'legacy_key' => ['dealer_id' => 22, 'collaborator_id' => 701],
                    'legacy_email' => '-',
                    'legacy_name' => 'Seller Placeholder',
                    'legacy_username' => 'seller.alpha',
                    'legacy_role_code' => 'collaboratore',
                    'legacy_dealer_code' => 'dealer-alpha',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-alpha', 'user_email' => '-'],
                    'legacy_user_email' => '-',
                    'legacy_user_username' => 'seller.alpha',
                    'legacy_organization_code' => 'dealer-alpha',
                    'legacy_role_code' => 'collaboratore',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '88',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'dealer-admin-88'],
                    'legacy_email' => null,
                    'legacy_username' => 'dealeradmin88',
                    'legacy_channel' => 'dealer_admin',
                ],
            ],
            'bootstrap-auth-org-edge-cases' => [
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'organization',
                    'status' => 'mapped_candidate',
                    'legacy_name' => 'Mac Solution Neo Workspace',
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-bootstrap', 'user_email' => 'admin@macsolution.test'],
                    'legacy_user_email' => 'admin@macsolution.test',
                    'legacy_organization_code' => null,
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'assignment',
                    'status' => 'mapped_candidate',
                    'legacy_key' => [
                        'dealer_code' => null,
                        'user_email' => 'admin@macsolution.test',
                        'assignment_role_code' => 'dealer_account_manager',
                    ],
                    'legacy_user_email' => 'admin@macsolution.test',
                    'legacy_dealer_code' => null,
                    'legacy_assignment_role_code' => 'dealer_account_manager',
                ],
            ],
            'bootstrap-auth-org-create-only' => [
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'organization',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '910',
                    'legacy_key' => ['dealer_code' => 'dealer-gamma', 'kind' => 'organization'],
                    'legacy_code' => 'dealer-gamma',
                    'legacy_name' => 'Dealer Gamma',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '911',
                    'legacy_key' => ['dealer_id' => 910, 'collaborator_id' => 911],
                    'legacy_email' => '-',
                    'legacy_name' => 'Seller Gamma',
                    'legacy_username' => 'seller.gamma',
                    'legacy_role_code' => 'collaboratore',
                    'legacy_dealer_code' => 'dealer-gamma',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-gamma', 'user_email' => '-'],
                    'legacy_user_email' => '-',
                    'legacy_user_username' => 'seller.gamma',
                    'legacy_organization_code' => 'dealer-gamma',
                    'legacy_role_code' => 'collaboratore',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'commerciali',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '912',
                    'legacy_key' => ['commerciale_id' => 912],
                    'legacy_email' => 'operator@dealer-gamma.test',
                    'legacy_name' => 'Dealer Gamma Operator',
                    'legacy_username' => 'operator.gamma',
                ],
                [
                    'legacy_table' => 'dealer_user_assignment',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-gamma', 'user_email' => 'operator@dealer-gamma.test'],
                    'legacy_user_email' => 'operator@dealer-gamma.test',
                    'legacy_organization_code' => 'dealer-gamma',
                    'target_membership_role_code' => 'dealer_seller',
                ],
                [
                    'legacy_table' => 'dealer_operatore_figura',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_id' => null,
                    'legacy_key' => [
                        'dealer_code' => 'dealer-gamma',
                        'operator_id' => 912,
                        'membership_role_code' => 'dealer_operator_member',
                    ],
                    'legacy_user_email' => 'operator@dealer-gamma.test',
                    'legacy_organization_code' => 'dealer-gamma',
                    'legacy_user_username' => 'operator.gamma',
                    'legacy_assignment_role_code' => 'dealer_operator',
                    'target_membership_role_code' => 'dealer_operator_member',
                ],
                [
                    'legacy_table' => 'dealer_operatore_figura',
                    'candidate' => 'assignment',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '913',
                    'legacy_key' => [
                        'dealer_code' => 'dealer-gamma',
                        'operator_id' => 912,
                        'assignment_role_code' => 'dealer_operator',
                    ],
                    'legacy_user_email' => 'operator@dealer-gamma.test',
                    'legacy_dealer_code' => 'dealer-gamma',
                    'legacy_assignment_role_code' => 'dealer_operator',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '914',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'dealer-gamma-admin'],
                    'legacy_username' => 'dealer.gamma.admin',
                    'legacy_channel' => 'dealer_admin',
                ],
            ],
            'bootstrap-auth-org-dependency-guards' => [
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'organization',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '920',
                    'legacy_key' => ['dealer_code' => 'dealer-dependency-theta', 'kind' => 'organization'],
                    'legacy_code' => 'dealer-dependency-theta',
                    'legacy_name' => 'Dealer Dependency Theta',
                ],
                [
                    'legacy_table' => 'dealer_collaboratore',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_key' => ['dealer_code' => 'dealer-dependency-theta', 'user_email' => ''],
                    'legacy_user_email' => null,
                    'legacy_user_username' => 'dependency.theta.admin',
                    'legacy_organization_code' => 'dealer-dependency-theta',
                    'legacy_role_code' => 'amministratore',
                    'target_membership_role_code' => 'dealer_admin',
                ],
                [
                    'legacy_table' => 'dealer_operatore_figura',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                    'legacy_id' => null,
                    'legacy_key' => [
                        'dealer_code' => 'dealer-dependency-theta',
                        'operator_id' => 921,
                        'membership_role_code' => 'dealer_operator_member',
                    ],
                    'legacy_user_email' => 'operator-dependency-921@macsolution.invalid',
                    'legacy_organization_code' => 'dealer-dependency-theta',
                    'legacy_user_username' => 'dependency.theta.operator',
                    'legacy_assignment_role_code' => 'commerciale',
                    'target_membership_role_code' => 'dealer_operator_member',
                ],
                [
                    'legacy_table' => 'dealer_operatore_figura',
                    'candidate' => 'assignment',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '921',
                    'legacy_key' => [
                        'dealer_code' => 'dealer-dependency-theta',
                        'operator_id' => 921,
                        'assignment_role_code' => 'commerciale',
                    ],
                    'legacy_user_email' => 'operator-dependency-921@macsolution.invalid',
                    'legacy_dealer_code' => 'dealer-dependency-theta',
                    'legacy_assignment_role_code' => 'commerciale',
                    'legacy_operator_username' => 'dependency.theta.operator',
                ],
            ],
            'bootstrap-auth-org-internal-principal-review' => [
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '1001',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'supporto-admin'],
                    'legacy_username' => 'supporto',
                    'legacy_channel' => 'dealer_admin',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '1002',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'admin-principal'],
                    'legacy_username' => 'admin',
                    'legacy_channel' => 'dealer_admin',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '1003',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'rosy-principal'],
                    'legacy_username' => 'rosy',
                    'legacy_channel' => 'dealer_admin',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '1004',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'bo-sync-principal'],
                    'legacy_username' => 'bo_sync',
                    'legacy_channel' => 'dealer_admin',
                ],
                [
                    'legacy_table' => 'dealer',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                    'legacy_id' => '1005',
                    'legacy_key' => ['channel' => 'admin', 'dealer_code' => 'mac-principal'],
                    'legacy_username' => 'mac',
                    'legacy_channel' => 'dealer_admin',
                ],
            ],
            default => [],
        };
    }
}
