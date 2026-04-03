<?php

namespace App\Application\Auth\LegacyImport;

use App\Application\Auth\LegacyImport\Contracts\AuthOrgLegacyDatasetAdapter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MySqlAuthOrgLegacyDatasetAdapter implements AuthOrgLegacyDatasetAdapter
{
    public function supports(string $dataset): bool
    {
        return $dataset === 'legacy-ci3-auth-org';
    }

    public function fetch(string $sourceSystem, string $legacyTable, int $batch): iterable
    {
        try {
            return $this->fetchViaConnection($legacyTable, $batch);
        } catch (Throwable $exception) {
            return $this->fetchViaMysqlCli($legacyTable, $batch);
        }
    }

    private function fetchViaConnection(string $legacyTable, int $batch): array
    {
        $connection = config('legacy_import.connection');
        $dealerTable = config('legacy_import.auth_org.tables.dealer');
        $membershipTable = config('legacy_import.auth_org.tables.membership');
        $collaboratorTable = config('legacy_import.auth_org.tables.collaborator');
        $commercialTable = config('legacy_import.auth_org.tables.commercial');
        $assignmentTable = config('legacy_import.auth_org.tables.assignment');

        $rows = collect();

        if (($legacyTable === 'all' || $legacyTable === 'dealer') && Schema::connection($connection)->hasTable($dealerTable)) {
            $dealerColumns = Schema::connection($connection)->getColumnListing($dealerTable);
            $dealerSelect = array_values(array_filter([
                'id',
                'tipo',
                in_array('email', $dealerColumns, true) ? 'email' : null,
                in_array('code', $dealerColumns, true) ? 'code' : null,
                in_array('name', $dealerColumns, true) ? 'name' : null,
                in_array('username', $dealerColumns, true) ? 'username' : null,
                in_array('ragione_sociale', $dealerColumns, true) ? 'ragione_sociale' : null,
                in_array('is_enable', $dealerColumns, true) ? 'is_enable' : null,
                in_array('id_commerciale', $dealerColumns, true) ? 'id_commerciale' : null,
            ]));

            $dealerRows = DB::connection($connection)
                ->table($dealerTable)
                ->select($dealerSelect)
                ->limit($batch)
                ->get()
                ->map(function ($row) {
                    $legacyCode = $row->code ?? $row->username ?? null;
                    $legacyName = $row->name ?? $row->ragione_sociale ?? null;
                    $legacyEmail = $row->email ?? null;

                    return [
                        'legacy_table' => 'dealer',
                        'legacy_id' => (string) $row->id,
                        'legacy_key' => ($row->tipo ?? null) === 'admin'
                            ? ['channel' => 'admin', 'dealer_code' => (string) ($legacyCode ?? $row->id)]
                            : ['dealer_code' => (string) ($legacyCode ?? $row->id), 'kind' => 'organization'],
                        'legacy_email' => $legacyEmail,
                        'legacy_code' => $legacyCode,
                        'legacy_name' => $legacyName,
                        'legacy_username' => $row->username ?? null,
                        'legacy_channel' => ($row->tipo ?? null) === 'admin' ? 'dealer_admin' : 'dealer_organization',
                        'candidate' => ($row->tipo ?? null) === 'admin' ? 'user' : 'organization',
                        'status' => 'mapped_candidate',
                    ];
                });

            $rows = $rows->concat($dealerRows);
        }

        if (($legacyTable === 'all' || $legacyTable === 'dealer_user_assignment') && Schema::connection($connection)->hasTable($membershipTable)) {
            $availableColumns = Schema::connection($connection)->getColumnListing($membershipTable);
            $selectedColumns = ['id', 'dealer_id', 'user_id', 'user_email', 'organization_code'];

            if (in_array('dealer_code', $availableColumns, true)) {
                $selectedColumns[] = 'dealer_code';
            }

            if (in_array('assignment_role_code', $availableColumns, true)) {
                $selectedColumns[] = 'assignment_role_code';
            }

            $membershipRows = DB::connection($connection)
                ->table($membershipTable)
                ->select($selectedColumns)
                ->limit($batch)
                ->get()
                ->flatMap(function ($row) {
                    $membershipCandidate = [
                        'legacy_table' => 'dealer_user_assignment',
                        'legacy_id' => (string) $row->id,
                        'legacy_key' => [
                            'dealer_code' => (string) ($row->organization_code ?? ''),
                            'user_email' => (string) ($row->user_email ?? ''),
                        ],
                        'legacy_user_email' => $row->user_email ?? null,
                        'legacy_organization_code' => $row->organization_code ?? null,
                        'candidate' => 'membership',
                        'status' => 'mapped_candidate',
                    ];

                    $assignmentCandidate = [
                        'legacy_table' => 'dealer_user_assignment',
                        'legacy_id' => null,
                        'legacy_key' => [
                            'dealer_code' => (string) (($row->dealer_code ?? null) ?: ($row->organization_code ?? '')),
                            'user_email' => (string) ($row->user_email ?? ''),
                            'assignment_role_code' => (string) ($row->assignment_role_code ?? 'dealer_operator'),
                        ],
                        'legacy_user_email' => $row->user_email ?? null,
                        'legacy_dealer_code' => ($row->dealer_code ?? null) ?: ($row->organization_code ?? null),
                        'legacy_assignment_role_code' => $row->assignment_role_code ?? 'dealer_operator',
                        'candidate' => 'assignment',
                        'status' => 'mapped_candidate',
                    ];

                    return [$membershipCandidate, $assignmentCandidate];
                });

            $rows = $rows->concat($membershipRows);
        }

        if (($legacyTable === 'all' || $legacyTable === 'dealer_collaboratore') && Schema::connection($connection)->hasTable($collaboratorTable) && Schema::connection($connection)->hasTable($dealerTable)) {
            $collaboratorRows = DB::connection($connection)
                ->table($collaboratorTable . ' as c')
                ->join($dealerTable . ' as d', 'd.id', '=', 'c.id_dealer')
                ->select([
                    'c.id',
                    'c.id_dealer',
                    'c.nominativo',
                    'c.username',
                    'c.email',
                    'c.ruolo',
                    'd.username as dealer_code',
                    'd.ragione_sociale as dealer_name',
                ])
                ->limit($batch)
                ->get();

            $rows = $rows->concat($collaboratorRows->map(fn ($row) => [
                'legacy_table' => 'dealer_collaboratore',
                'legacy_id' => (string) $row->id,
                'legacy_key' => ['dealer_id' => (int) $row->id_dealer, 'collaborator_id' => (int) $row->id],
                'legacy_email' => $row->email ?? null,
                'legacy_name' => $row->nominativo ?? null,
                'legacy_username' => $row->username ?? null,
                'legacy_role_code' => $row->ruolo ?? null,
                'legacy_dealer_code' => $row->dealer_code ?? null,
                'legacy_dealer_name' => $row->dealer_name ?? null,
                'target_membership_role_code' => $this->collaboratorMembershipRole($row->ruolo ?? null),
                'candidate' => 'user',
                'status' => 'mapped_candidate',
            ]));

            $rows = $rows->concat($collaboratorRows->map(fn ($row) => [
                'legacy_table' => 'dealer_collaboratore',
                'legacy_id' => null,
                'legacy_key' => ['dealer_code' => (string) ($row->dealer_code ?? ''), 'user_email' => (string) ($row->email ?? '')],
                'legacy_user_email' => $row->email ?? null,
                'legacy_user_username' => $row->username ?? null,
                'legacy_organization_code' => $row->dealer_code ?? null,
                'legacy_role_code' => $row->ruolo ?? null,
                'target_membership_role_code' => $this->collaboratorMembershipRole($row->ruolo ?? null),
                'candidate' => 'membership',
                'status' => 'mapped_candidate',
            ]));
        }

        if (($legacyTable === 'all' || $legacyTable === 'commerciali') && Schema::connection($connection)->hasTable($commercialTable)) {
            $commercialRows = DB::connection($connection)
                ->table($commercialTable)
                ->select(['id', 'nominativo', 'username', 'email', 'figura'])
                ->limit($batch)
                ->get()
                ->map(fn ($row) => [
                    'legacy_table' => 'commerciali',
                    'legacy_id' => (string) $row->id,
                    'legacy_key' => ['commerciale_id' => (int) $row->id],
                    'legacy_email' => $row->email ?? null,
                    'legacy_name' => $row->nominativo ?? null,
                    'legacy_username' => $row->username ?? null,
                    'legacy_assignment_role_code' => $row->figura ?? 'dealer_commercial',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                ]);

            $rows = $rows->concat($commercialRows);
        }

        if (($legacyTable === 'all' || $legacyTable === 'dealer_operatore_figura') && Schema::connection($connection)->hasTable($assignmentTable) && Schema::connection($connection)->hasTable($dealerTable)) {
            $assignmentQuery = DB::connection($connection)
                ->table($assignmentTable . ' as a')
                ->join($dealerTable . ' as d', 'd.id', '=', 'a.dealer_id');

            if (Schema::connection($connection)->hasTable($commercialTable)) {
                $assignmentQuery->leftJoin($commercialTable . ' as cm', 'cm.id', '=', 'a.operatore_id');
            }

            if (Schema::connection($connection)->hasTable($collaboratorTable)) {
                $assignmentQuery->leftJoin($collaboratorTable . ' as dc', 'dc.id', '=', 'a.operatore_id');
            }

            $assignmentRows = $assignmentQuery
                ->selectRaw("
                    a.id,
                    a.dealer_id,
                    a.operatore_id,
                    a.figura,
                    d.username as dealer_code,
                    COALESCE(cm.email, dc.email) as operator_email,
                    COALESCE(cm.username, dc.username) as operator_username
                ")
                ->limit($batch)
                ->get()
                ->flatMap(function ($row) {
                    $membershipCandidate = [
                        'legacy_table' => 'dealer_operatore_figura',
                        'legacy_id' => null,
                        'legacy_key' => [
                            'dealer_code' => (string) ($row->dealer_code ?? ''),
                            'operator_id' => (int) $row->operatore_id,
                            'membership_role_code' => 'dealer_operator_member',
                        ],
                        'legacy_user_email' => $row->operator_email ?? null,
                        'legacy_user_username' => $row->operator_username ?? null,
                        'legacy_organization_code' => $row->dealer_code ?? null,
                        'legacy_assignment_role_code' => $row->figura ?? 'dealer_operator',
                        'target_membership_role_code' => 'dealer_operator_member',
                        'candidate' => 'membership',
                        'status' => 'mapped_candidate',
                    ];

                    $assignmentCandidate = [
                        'legacy_table' => 'dealer_operatore_figura',
                        'legacy_id' => (string) $row->id,
                        'legacy_key' => [
                            'dealer_code' => (string) ($row->dealer_code ?? ''),
                            'operator_id' => (int) $row->operatore_id,
                            'assignment_role_code' => (string) ($row->figura ?? 'dealer_operator'),
                        ],
                        'legacy_user_email' => $row->operator_email ?? null,
                        'legacy_dealer_code' => $row->dealer_code ?? null,
                        'legacy_assignment_role_code' => $row->figura ?? 'dealer_operator',
                        'legacy_operator_username' => $row->operator_username ?? null,
                        'candidate' => 'assignment',
                        'status' => 'mapped_candidate',
                    ];

                    return [$membershipCandidate, $assignmentCandidate];
                });

            $rows = $rows->concat($assignmentRows);
        }

        return $rows->take($batch)->values()->all();
    }

    private function fetchViaMysqlCli(string $legacyTable, int $batch): array
    {
        $database = (string) config('database.connections.' . config('legacy_import.connection') . '.database');
        if ($database === '') {
            throw new RuntimeException('Legacy import CLI fallback cannot resolve database name.');
        }

        $rows = collect();

        if ($legacyTable === 'all' || $legacyTable === 'dealer') {
            foreach ($this->mysqlRows(sprintf(
                'SELECT id, tipo, username, ragione_sociale FROM %s.%s LIMIT %d',
                $database,
                config('legacy_import.auth_org.tables.dealer'),
                $batch,
            )) as $line) {
                [$id, $tipo, $username, $ragioneSociale] = array_pad(explode("\t", $line), 4, null);

                $rows->push([
                    'legacy_table' => 'dealer',
                    'legacy_id' => (string) $id,
                    'legacy_key' => $tipo === 'admin'
                        ? ['channel' => 'admin', 'dealer_code' => (string) ($username ?: $id)]
                        : ['dealer_code' => (string) ($username ?: $id), 'kind' => 'organization'],
                    'legacy_email' => null,
                    'legacy_code' => $username,
                    'legacy_name' => $ragioneSociale,
                    'legacy_username' => $username,
                    'legacy_channel' => $tipo === 'admin' ? 'dealer_admin' : 'dealer_organization',
                    'candidate' => $tipo === 'admin' ? 'user' : 'organization',
                    'status' => 'mapped_candidate',
                ]);
            }
        }

        if ($legacyTable === 'all' || $legacyTable === 'dealer_collaboratore') {
            foreach ($this->mysqlRows(sprintf(
                "SELECT c.id, c.id_dealer, c.nominativo, c.username, COALESCE(c.email, ''), COALESCE(c.ruolo, ''), d.username FROM %s.%s c JOIN %s.%s d ON d.id = c.id_dealer LIMIT %d",
                $database,
                config('legacy_import.auth_org.tables.collaborator'),
                $database,
                config('legacy_import.auth_org.tables.dealer'),
                $batch,
            )) as $line) {
                [$id, $dealerId, $nominativo, $username, $email, $ruolo, $dealerCode] = array_pad(explode("\t", $line), 7, null);
                $email = $this->normalizeEmail($email);

                $rows->push([
                    'legacy_table' => 'dealer_collaboratore',
                    'legacy_id' => (string) $id,
                    'legacy_key' => ['dealer_id' => (int) $dealerId, 'collaborator_id' => (int) $id],
                    'legacy_email' => $email,
                    'legacy_name' => $nominativo,
                    'legacy_username' => $username,
                    'legacy_role_code' => $ruolo !== '' ? $ruolo : null,
                    'legacy_dealer_code' => $dealerCode,
                    'target_membership_role_code' => $this->collaboratorMembershipRole($ruolo),
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                ]);

                $rows->push([
                    'legacy_table' => 'dealer_collaboratore',
                    'legacy_id' => null,
                    'legacy_key' => ['dealer_code' => (string) ($dealerCode ?? ''), 'user_email' => (string) ($email ?? '')],
                    'legacy_user_email' => $email,
                    'legacy_user_username' => $username,
                    'legacy_organization_code' => $dealerCode,
                    'legacy_role_code' => $ruolo !== '' ? $ruolo : null,
                    'target_membership_role_code' => $this->collaboratorMembershipRole($ruolo),
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                ]);
            }
        }

        if ($legacyTable === 'all' || $legacyTable === 'commerciali') {
            foreach ($this->mysqlRows(sprintf(
                "SELECT id, nominativo, username, COALESCE(email, ''), figura FROM %s.%s LIMIT %d",
                $database,
                config('legacy_import.auth_org.tables.commercial'),
                $batch,
            )) as $line) {
                [$id, $nominativo, $username, $email, $figura] = array_pad(explode("\t", $line), 5, null);

                $rows->push([
                    'legacy_table' => 'commerciali',
                    'legacy_id' => (string) $id,
                    'legacy_key' => ['commerciale_id' => (int) $id],
                    'legacy_email' => $this->normalizeEmail($email),
                    'legacy_name' => $nominativo,
                    'legacy_username' => $username,
                    'legacy_assignment_role_code' => $figura ?: 'dealer_commercial',
                    'candidate' => 'user',
                    'status' => 'mapped_candidate',
                ]);
            }
        }

        if ($legacyTable === 'all' || $legacyTable === 'dealer_operatore_figura') {
            foreach ($this->mysqlRows(sprintf(
                "SELECT a.id, a.dealer_id, a.operatore_id, a.figura, d.username, COALESCE(cm.email, dc.email, ''), COALESCE(cm.username, dc.username, '') FROM %s.%s a JOIN %s.%s d ON d.id = a.dealer_id LEFT JOIN %s.%s cm ON cm.id = a.operatore_id LEFT JOIN %s.%s dc ON dc.id = a.operatore_id LIMIT %d",
                $database,
                config('legacy_import.auth_org.tables.assignment'),
                $database,
                config('legacy_import.auth_org.tables.dealer'),
                $database,
                config('legacy_import.auth_org.tables.commercial'),
                $database,
                config('legacy_import.auth_org.tables.collaborator'),
                $batch,
            )) as $line) {
                [$id, $dealerId, $operatoreId, $figura, $dealerCode, $operatorEmail, $operatorUsername] = array_pad(explode("\t", $line), 7, null);

                $rows->push([
                    'legacy_table' => 'dealer_operatore_figura',
                    'legacy_id' => null,
                    'legacy_key' => [
                        'dealer_code' => (string) ($dealerCode ?? ''),
                        'operator_id' => (int) $operatoreId,
                        'membership_role_code' => 'dealer_operator_member',
                    ],
                    'legacy_user_email' => $this->normalizeEmail($operatorEmail),
                    'legacy_user_username' => $operatorUsername !== '' ? $operatorUsername : null,
                    'legacy_organization_code' => $dealerCode,
                    'legacy_assignment_role_code' => $figura ?: 'dealer_operator',
                    'target_membership_role_code' => 'dealer_operator_member',
                    'candidate' => 'membership',
                    'status' => 'mapped_candidate',
                ]);

                $rows->push([
                    'legacy_table' => 'dealer_operatore_figura',
                    'legacy_id' => (string) $id,
                    'legacy_key' => [
                        'dealer_code' => (string) ($dealerCode ?? ''),
                        'operator_id' => (int) $operatoreId,
                        'assignment_role_code' => (string) ($figura ?: 'dealer_operator'),
                    ],
                    'legacy_user_email' => $this->normalizeEmail($operatorEmail),
                    'legacy_dealer_code' => $dealerCode,
                    'legacy_assignment_role_code' => $figura ?: 'dealer_operator',
                    'legacy_operator_username' => $operatorUsername !== '' ? $operatorUsername : null,
                    'candidate' => 'assignment',
                    'status' => 'mapped_candidate',
                ]);
            }
        }

        return $rows->take($batch)->values()->all();
    }

    /**
     * @return array<int, string>
     */
    private function mysqlRows(string $sql): array
    {
        $binary = (string) config('legacy_import.cli.binary', 'mysql');
        $user = (string) config('legacy_import.cli.user', 'root');

        $command = sprintf(
            '%s -u%s -p --batch --raw --skip-column-names -e %s 2>NUL',
            $binary,
            $user,
            escapeshellarg($sql),
        );

        $output = shell_exec($command);

        if (! is_string($output)) {
            throw new RuntimeException('mysql cli returned no output');
        }

        $trimmed = trim($output);

        if ($trimmed === '') {
            return [];
        }

        return array_values(array_filter(preg_split("/\\r\\n|\\n|\\r/", $trimmed) ?: [], fn ($line) => $line !== ''));
    }

    private function normalizeEmail(mixed $value): ?string
    {
        $email = strtolower(trim((string) $value));

        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function collaboratorMembershipRole(mixed $legacyRole): string
    {
        return strtolower(trim((string) $legacyRole)) === 'amministratore'
            ? 'dealer_admin'
            : 'dealer_seller';
    }
}
