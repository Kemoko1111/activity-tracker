<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create activity_logs table to record each status update as an immutable log entry.
     * Each entry captures who updated, what status, and any remark — never overwritten.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');                              // The day this log belongs to
            $table->string('status');                          // 'done' or 'pending'
            $table->text('remark')->nullable();                // Free-text remark
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('date');
            $table->index(['activity_id', 'date']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
