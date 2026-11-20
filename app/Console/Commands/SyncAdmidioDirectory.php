<?php

namespace App\Console\Commands;

use App\Services\AdmidioDirectoryService;
use Illuminate\Console\Command;

class SyncAdmidioDirectory extends Command
{
    protected $signature = 'itam:sync-admidio';

    protected $description = 'Synchronize departments and employees from the Admidio database into ITAM tables';

    public function handle(AdmidioDirectoryService $service): int
    {
        $this->info('Starting synchronization from Admidio database...');

        $result = $service->syncAll();

        $this->info("Successfully synchronized {$result['departments']} departments and {$result['employees']} employees from Admidio.");

        return self::SUCCESS;
    }
}
