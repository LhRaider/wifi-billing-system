<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Throwable;

class SyncMikrotikSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mikrotik:sync-sessions';

    /**
     * The console command description.
     */
    protected $description =
        'Synchronize active MikroTik Hotspot sessions with the billing system';

    /**
     * Execute the console command.
     */
    public function handle(
        MikrotikService $mikrotikService
    ): int {
        $routers = Router::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($routers->isEmpty()) {
            $this->info('No active MikroTik routers found.');

            return self::SUCCESS;
        }

        $successful = 0;
        $failed = 0;
        $created = 0;
        $updated = 0;
        $closed = 0;
        $ignored = 0;

        foreach ($routers as $router) {
            try {
                $result =
                    $mikrotikService->syncHotspotSessions(
                        $router
                    );

                if (!($result['success'] ?? false)) {
                    $failed++;

                    $this->warn(
                        $router->name
                        . ': '
                        . ($result['message']
                            ?? 'Session synchronization failed.')
                    );

                    continue;
                }

                $successful++;

                $created +=
                    (int) ($result['created'] ?? 0);

                $updated +=
                    (int) ($result['updated'] ?? 0);

                $closed +=
                    (int) ($result['closed'] ?? 0);

                $ignored +=
                    (int) ($result['ignored'] ?? 0);

                $this->info(
                    $router->name
                    . ': '
                    . ($result['message']
                        ?? 'Sessions synchronized.')
                );
            } catch (Throwable $e) {
                $failed++;

                report($e);

                $this->error(
                    $router->name
                    . ': unexpected session synchronization failure.'
                );
            }
        }

        $this->newLine();

        $this->info(
            'Session synchronization complete. '
            . 'Routers successful: '
            . $successful
            . ', failed: '
            . $failed
            . ', sessions created: '
            . $created
            . ', updated: '
            . $updated
            . ', closed: '
            . $closed
            . ', ignored: '
            . $ignored
            . '.'
        );

        /*
         * A router communication failure is reported and logged by
         * the MikroTik service, but it does not cause sessions to be
         * falsely closed.
         *
         * Returning success here also prevents a temporary failure of
         * one ISP router from treating the entire scheduled command as
         * a platform failure.
         */
        return self::SUCCESS;
    }
}