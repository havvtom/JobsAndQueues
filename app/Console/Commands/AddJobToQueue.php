<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\CustomJob;
use InvalidArgumentException;

class AddJobToQueue extends Command
{
    protected $signature = 'job:add {className} {method} {--parameters=} {--priority=1} {--delay=0} {--max_retries=3}';
    protected $description = 'Add a job to the queue';

    public function handle()
    {
        try {
            $className = $this->argument('className');
            $method = $this->argument('method');
            $parameters = json_decode($this->option('parameters'), true) ?? [];
            $priority = (int) $this->option('priority');
            $maxRetries = (int) $this->option('max_retries');
            $delay = (int) $this->option('delay');

            // Security check for allowed classes
            $allowedClasses = config('backgroundjobs.allowed_classes');
            if ( !in_array($className, $allowedClasses)) {
                throw new InvalidArgumentException("Unauthorized class or method: {$className}::{$method}");
            }            

            // Validate class and method name formats
            $this->validateClassAndMethod($className, $method);

            CustomJob::create([
                'name' => "{$className}::{$method}",
                'class_name' => $className,
                'method' => $method,
                'parameters' => json_encode($parameters),
                'priority' => $priority,
                'status' => 'pending',
                'scheduled_at' => now()->addSeconds($delay),
                'max_retries' => $maxRetries
            ]);

            $this->info("Job added to queue: {$className}::{$method}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error("Failed to add job to queue: " . $e->getMessage(), [
                'class' => $className ?? null,
                'method' => $method ?? null,
            ]);
            return Command::FAILURE;
        }
    }

    // Validate class and method name formats
    public function validateClassAndMethod($class, $method)
    {
        // Check class name format
        if (!preg_match('/^[A-Za-z0-9_\\\\]+$/', $class)) {
            Log::error("Invalid class name: {$class}");
            return false;
        }

        // Check method name format
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $method)) {
            Log::error("Invalid method name: {$method}");
            return false;
        }

        return true;
    }
}
