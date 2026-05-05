<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Improvement #8: WithSavedFilters (database driver)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datatable_filter_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('model');
            $table->string('name');
            $table->json('filters');
            $table->timestamps();

            $table->unique(['user_id', 'model', 'name']);
            $table->index(['user_id', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datatable_filter_presets');
    }
};
