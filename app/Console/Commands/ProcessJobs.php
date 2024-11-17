<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessJobs extends Command
{
    protected $signature = 'jobs:process';
    protected $description = 'Process Background Jobs';

    public function handle()
    {
        $this->info("Custom Job Worker Started...");

        // Call the helper function to start processing jobs
        runBackgroundJob();

        $this->info("Custom Job Worker Stopped...");
    }
}
