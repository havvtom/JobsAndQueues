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