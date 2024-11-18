<?php
namespace App\Jobs;

use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;

class ImportLoansJob
{
    protected $job;  // Store the CustomJob model

    // Modify the constructor to accept the CustomJob model
    public function __construct(CustomJob $job)
    {
        $this->job = $job;
    }

    public function handle()
    {
        
        try {
            $parameters = $this->job->parameters;  // Get parameters from the job model

            while(true){

            }
            // job logic here
            Log::info('Job executed successfully', ['data' => $parameters]);

            // Mark the job as completed
            
            $updateJob = CustomJob::find($this->job->id);
            $updateJob->status = 'completed';
            $updateJob->save();

        } catch (\Exception $e) {
            // Log failure
            Log::error('Job execution failed', [
                'error' => $e->getMessage(),
                'job_name' => $this->job->name
            ]);

            // Mark the job as failed
            $this->job->status = 'failed';
            $this->job->error = $e->getMessage();
            $this->job->save();
        }
    }
}
