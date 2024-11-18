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

The queue worker can be started from the dashboard by clicking the "Process Pending Tasks" button. Optionally, you can also use the command line to start the worker:

```bash
php artisan job:process
```