# Jobs and Queues Project

## Overview

The Jobs and Queues Project is a background job management system built with Laravel. It allows users to add jobs to a queue, execute them with delays, and track their status. The system supports job prioritization, retry logic, and provides an easy-to-use interface for managing jobs.

## Features

- **Job Creation**: Easily add custom jobs to the queue with class names, methods, parameters, priority, and delay.
- **Job Prioritization**: Supports job priority, with higher priority jobs being processed first.
- **Delay Support**: Schedule jobs to run at a future time by specifying a delay in seconds.
- **Retry Logic**: Automatically retry jobs up to a specified number of attempts if they fail.
- **Job Status**: Track job statuses such as `pending`, `running`, `completed`, and `failed`.
- **Logging**: Comprehensive logging of job processing, including success and failure events.

## Prerequisites

Ensure you have the following before setting up the project:

- **PHP** (7.3 or higher)
- **Laravel** (8.x or higher)
- **MySQL** or **PostgreSQL** for the database
- **Composer** for dependency management
- **Git** for version control

## Installation

### 1. Clone the repository:

```bash
git clone https://github.com/havvtom/JobsAndQueues.git
```
### 2. Navigate to the project directory

Change into the project directory to begin setup:

```bash
cd JobsAndQueues
```
### 3. Install dependencies

Use Composer to install all necessary dependencies for the project:

```bash
composer install
```
### 4. Set up the environment file

Copy the example environment configuration file and edit it to match your local setup:

```bash
cp .env.example .env
```
### 5. Generate the application key

Generate a unique application key for your Laravel application:

```bash
php artisan key:generate
```
### 6. Run database migrations

Run the database migrations to create the necessary tables for job management:

```bash
php artisan migrate
```

### 7. Run `php artisan serve` and `npm run dev`

The front end is built using Inertia and Vue.js. There are options to add a job to the queue using the dashboard or the command line.

#### Using the Command Line

## Usage

### Adding Jobs to the Queue

Add a job to the queue by running the following command:

```bash
php artisan job:add {className} {method} {--parameters=} {--priority=1} {--delay=0} {--max_retries=3}
```
#### Parameters:

- `--className`: The class containing the method to execute.
- `method`: The method name to call on the class.
- `--parameters`: A JSON string containing parameters for the method.
- `--priority`: The priority of the job (default: 1).
- `--delay`: The delay in seconds before the job runs (default: 0).
- `--max_retries`: The maximum number of retries if the job fails (default: 3).

To add a job through the dashboard, simply navigate to the home URL and click the "Add to Queue" button.

### 7. Job Status

Jobs have the following statuses:

- `--pending`: Job is waiting to be processed.
- `--running`: Job is currently being processed.
- `--completed`: Job has been processed successfully.
- `--failed`: Job has failed and is subject to retry logic.

You can check the job status in the custom_jobs table in your database or on the dashboard.

### Job Logs
Job success and failure events are logged in the storage/logs/laravel.log file.

### Custom Job Class
### Creating a Custom Job

To create a custom job, define a job class inside the `app/Jobs` directory. Here's an example of how to define a job class:

You can use the following example job. All files in the `App\Jobs` directory are automatically scanned when using the dashboard and can be selected from the dropdown when adding jobs to the queue.

```php
<?php

namespace App\Jobs;

use App\Models\CustomJob;
use Illuminate\Support\Facades\Log;

class ExampleJob
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

            // Job logic here
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
```
The queue worker can be started from the dashboard by clicking the "Process Pending Tasks" button. Optionally, you can also use the command line to start the worker:

```bash
php artisan job:process
```
### Simulating Cancelling a Running Job

To simulate canceling a running job, we have added an example job class `App\Jobs\ImportLoansJob`, which contains a while loop to represent a long-running process. Follow these steps:

1. Add `ImportLoansJob` to the queue.
2. Click the **Process Pending Tasks** button on the dashboard to start the job.
3. After clicking **Process Pending Tasks**, the job status will change from `pending` to `running` once the queue worker starts processing the job.
4. Refresh the home page to see the updated status.
5. When the job is running, a **Cancel** button will appear, which can be used to cancel the running job.

This example helps simulate the cancellation of a running job, which can be useful for testing or managing long-running tasks in the queue.

### Simulating and Testing Job Retries

To simulate and test retries, I have added the `SendEmailsJob` which is designed to throw an error during execution. This allows you to test the retry logic in your background job processing system. Here’s how the process works:

1. **Job Failure Simulation:**
   - The `SendEmailsJob` is intentionally designed to fail by throwing an error during its execution.
   - When this job is processed, it will immediately fail and trigger the retry logic.

2. **Retry Logic:**
   - After the job fails, it will be retried after one minute.
   - Each time the job fails, the `retry_count` increases, and the system will attempt to process the job again.

3. **Max Retries:**
   - The retry logic will allow the job to retry up to three times.
   - After the third failed attempt, the job’s status will be updated to `failed`, and it will no longer be retried.

4. **Testing:**
   - You can simulate this behavior by adding the `SendEmailsJob` to the queue and then triggering the job processing.
   - After each retry, you can check the job’s status and retry count to ensure the logic is functioning correctly.

5. **How to Test:**
   - Add the `SendEmailsJob` to the queue.
   - Use the "Process Pending Tasks" button in the dashboard or run the following command to start processing jobs:

     ```bash
     php artisan job:process
     ```

   - Refresh the page to check the job’s status. The job will attempt to run and, upon failure, will retry according to the retry settings.
   - After three failed attempts, the job status will be updated to `failed`, and no further retries will be attempted.

By following these steps, you can test the retry functionality and ensure that your system handles failed jobs and retries correctly.

