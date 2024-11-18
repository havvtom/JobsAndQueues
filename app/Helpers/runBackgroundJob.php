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

        // Instantiate the job class dynamically and pass the CustomJob instance to the job constructor
        $instance = app($job->class_name, ['job' => $job]);

        // Call the job's method (e.g., 'handle') and capture the response
        $response = call_user_func([$instance, $job->method]);

        // Assuming the response is a JSON response or an array with success/failure status
        $responseData = json_decode($response->getContent(), true);

        if (isset($responseData['status']) && $responseData['status'] === 'failed') {
            // Log failure
            Log::error("Job Failed: {$job->name}", [
                'error' => $responseData['message'] ?? 'Unknown error'
            ]);

            // Mark the job as failed
            $job->status = 'failed';
            $job->error = $responseData['message'] ?? 'Unknown error';
            $job->retry_count += 1;

            // Retry logic: move back to 'pending' if retry count is less than max retries
            if ($job->retry_count < $job->max_retries) {
                $job->status = 'pending';
                $job->scheduled_at = now()->addMinutes(1); // Delay the retry by 1 minutes
                Log::info("Job {$job->name} will retry in 5 minutes.");
            }

            $job->save();
        } else {
            // Mark the job as completed
            $job->status = 'completed';
            $job->save();

            Log::info("Job Completed: {$job->name}");
        }
    }
}
