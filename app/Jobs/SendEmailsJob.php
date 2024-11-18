<?php
namespace App\Jobs;

use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;
use Exception;

class SendEmailsJob
{
    protected $job;  // Store the CustomJob model

    public function __construct(CustomJob $job)
    {
        $this->job = $job;
    }

    public function handle()
    {
        try {
            $parameters = $this->job->parameters;  // Get parameters from the job model

            // Your job logic here
            Log::info('Sending emails', ['data' => $parameters]);

            throw new Exception("Error Processing Request", 1);
            

        } catch (Exception $e) {
            // Log failure
            Log::error('Job execution failed', [
                'error' => $e->getMessage(),
                'job_name' => $this->job->name
            ]);

            // Simulate job failure by returning a failure response
            return response()->json([
                'status' => 'failed',
                'message' => 'Job execution failed',
                'error' => $e->getMessage(),
                'job_id' => $this->job->id,
            ]);
        }
    }
}
