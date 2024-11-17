<?php
use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    /**
     * Process Custom Jobs from the queue (run once)
     *
     * @return void
     */
    function runBackgroundJob()
    {
        // Fetch the highest priority job that is scheduled and pending
        $job = CustomJob::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_at', 'asc')
            ->first();

        if (!$job) {
            // No jobs to process
            Log::info("No jobs available to process.");
            return;
        }

        Log::info("Processing Job: {$job->name}");

        // Mark the job as running
        $job->status = 'running';
        $job->save();

        try {
            
            $instance = app($job->class_name, ['job' => $job]);  // Pass CustomJob instance to the job constructor

            call_user_func([$instance, $job->method]);

            // Mark as completed
            $job->status = 'completed';
            $job->save();

            Log::info("Job Completed: {$job->name}");
        } catch (\Exception $e) {
            // Log failure and retry logic
            Log::error("Job Failed: {$job->name}", [
                'error' => $e->getMessage()
            ]);

            $job->status = 'failed';
            $job->error = $e->getMessage();
            $job->retry_count += 1;

            // Retry logic: move back to 'pending' if retry count is less than 3
            if ($job->retry_count < $job->max_retries) {
                $job->status = 'pending';
            }

            $job->save();
        }
    }
}
