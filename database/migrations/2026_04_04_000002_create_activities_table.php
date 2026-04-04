<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the activities table to store daily trackable activities.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');                           // e.g., "Daily SMS count vs logs"
            $table->text('description')->nullable();           // Optional details
            $table->string('category')->nullable();            // Grouping category
            $table->boolean('is_active')->default(true);       // Whether currently tracked
            $table->timestamps();

            // Indexes for filtering
            $table->index('is_active');
            $table->index('category');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
