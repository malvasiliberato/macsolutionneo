<?php

namespace App\Console\Commands\LegacyImport;

use App\Application\Auth\LegacyImport\AuthOrgLegacyCreateOnlyCommitGate;
use App\Application\Auth\LegacyImport\AuthOrgLegacyDryRun;
use Throwable;
use Illuminate\Console\Command;

class AuthOrgLegacyImportCommand extends Command
{
    protected $signature = 'legacy:import:auth-org
        {--source-system=legacy_ci3 : Logical source system name}
        {--legacy-table=all : Legacy table or dataset slice to inspect}
        {--dataset=bootstrap-auth-org : Controlled dataset to analyze in this slice}
        {--batch=100 : Max records to analyze}
        {--dry-run : Execute without writing targets or mappings}
        {--commit-create-only : Execute the guarded create-only gate}
        {--confirm-create-only : Required acknowledgement for create-only commit mode}';

    protected $description = 'Run the minimal auth/org legacy import dry-run contract.';

    public function handle(AuthOrgLegacyDryRun $dryRun, AuthOrgLegacyCreateOnlyCommitGate $createOnlyGate): int
    {
        if ($this->option('commit-create-only')) {
            if (! $this->option('confirm-create-only')) {
                $this->error('Create-only commit mode requires --confirm-create-only.');

                return self::INVALID;
            }

            try {
                $report = $createOnlyGate->run(
                    (string) $this->option('source-system'),
                    (string) $this->option('legacy-table'),
                    (string) $this->option('dataset'),
                    max((int) $this->option('batch'), 1),
                );
            } catch (Throwable $exception) {
                $this->error('Create-only gate failed before commit. Re-run with --dry-run to inspect candidates first.');

                return self::FAILURE;
            }

            $this->info('Auth/Org create-only commit summary');
            $this->newLine();

            foreach ($report['summary'] as $key => $value) {
                $this->line(sprintf('%s=%s', $key, is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
            }

            $this->newLine();
            $this->info('Auth/Org create-only gate snapshot');
            $this->newLine();

            foreach ($report['gate'] as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }

                $this->line(sprintf('%s=%s', $key, is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
            }

            return self::SUCCESS;
        }

        if (! $this->option('dry-run')) {
            $this->error('Commit mode is not available in this slice. Re-run with --dry-run or --commit-create-only.');

            return self::INVALID;
        }

        try {
            $report = $dryRun->run(
                (string) $this->option('source-system'),
                (string) $this->option('legacy-table'),
                (string) $this->option('dataset'),
                max((int) $this->option('batch'), 1),
                true,
            );
        } catch (Throwable $exception) {
            $this->error('Legacy adapter is not configured or reachable. Configure LEGACY_IMPORT_* or use --dataset=bootstrap-auth-org.');

            return self::FAILURE;
        }

        $this->info('Auth/Org legacy import dry-run summary');
        $this->newLine();

        foreach ($report['summary'] as $key => $value) {
            $this->line(sprintf('%s=%s', $key, is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
        }

        $this->newLine();
        $this->info('Auth/Org legacy reconciliation snapshot');
        $this->newLine();

        foreach ($report['reconciliation'] as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $this->line(sprintf('%s=%s', $key, is_bool($value) ? ($value ? 'true' : 'false') : (string) $value));
        }

        return self::SUCCESS;
    }
}
