<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('class_name');
            $table->string('method');
            $table->text('parameters')->nullable(); 
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->integer('priority')->default(1); // Higher priority = more urgent
            $table->integer('retry_count')->default(0); // Number of retry attempts
            $table->integer('max_retries')->default(3); // User-configured maximum retries
            $table->timestamp('scheduled_at')->nullable(); // Delay execution
            $table->text('error')->nullable(); // Error messages
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_jobs');
    }
};
