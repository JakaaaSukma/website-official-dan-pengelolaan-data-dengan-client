<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;

class SyncMembersFromGoogleSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync members from Google Sheet Template Laporan to local database';

    /**
     * Execute the console command.
     */
    public function handle(GoogleSheetService $googleSheetService)
    {
        $this->info('Starting member sync from Google Sheet...');
        
        $syncedCount = $googleSheetService->syncTemplateReportToDatabase(true);
        
        if ($syncedCount === false) {
            $this->error('Failed to sync members from Google Sheet! Check logs for details.');
            return 1;
        }
        
        $this->info("Successfully synced {$syncedCount} members!");
        return 0;
    }
}
