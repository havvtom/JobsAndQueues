<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomJob;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use Illuminate\Support\Facades\Artisan;

class CustomJobsController extends Controller
{
    public function index()
    {
        $jobClasses = $this->getJobClasses();
        $jobs = CustomJob::orderBy('priority', 'desc')->orderBy('created_at', 'asc')->get();
        return Inertia::render('Home', [
            'jobs' => $jobs,
            'jobClasses' => $jobClasses
        ]);
    }

    // Cancel a running job
    public function cancel($id)
    {
        $job = CustomJob::findOrFail($id);
        $job->status = 'canceled';
        $job->save();

        return redirect()->back()->with('success', 'Job canceled successfully.');
    }

    // Retry a failed job
    public function retry($id)
    {
        $job = CustomJob::findOrFail($id);
        $job->status = 'pending';
        $job->retry_count += 1;
        $job->save();

        return redirect()->back()->with('success', 'Job marked for retry.');
    }

    public function processPending(){
        // Log::info("Custom Job Worker Started...");

        // Call the helper function to start processing jobs
        runBackgroundJob();

        // Log::info("Custom Job Worker Stopped...");
    }

    public function store(Request $request)
    {

        $className = $request->data['name'];  
        $method = 'handle';       
        $parameters = $request->data['parameters'] ?: [];
        $priority = $request->data['priority'];
        $maxRetries = $request->data['maxRetries'];
        $delay = $request->data['delayInSeconds'];

        Artisan::call('job:add', [
            'className' => $className,
            'method' => $method,
            '--parameters' => json_encode($parameters),
            '--delay' => $delay,
            '--priority' => $priority,
            '--max_retries' => $maxRetries,
        ]);

        return back();
    }

    public function updateJob(Request $request){

        $job = CustomJob::find($request->data['id']);
    
        $job->parameters = $request->data['parameters'];
        $job->priority = $request->data['priority'];
        $job->max_retries = $request->data['maxRetries'];
        $job->scheduled_at = now()->addSeconds($request->data['delayInSeconds']);

        $job->save();
        
        return back();

    }

    private function getJobClasses()
    {
        $jobClasses = [];
        $path = app_path('Jobs');

        // Get all PHP files in the Jobs directory
        $files = File::allFiles($path);

        foreach ($files as $file) {
            // Generate the class namespace from the file path
            $className = $this->getClassNameFromFile($file->getPathname());

            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                $jobClasses[] = $className;
  
            }
        }

        return $jobClasses;
    }

    private function getClassNameFromFile($filePath)
    {
        // Convert the file path into a namespace
        $relativePath = Str::after($filePath, app_path() . DIRECTORY_SEPARATOR);
        $classPath = str_replace(
            [DIRECTORY_SEPARATOR, '.php'],
            ['\\', ''],
            $relativePath
        );

        return 'App\\' . $classPath;
    }
}
