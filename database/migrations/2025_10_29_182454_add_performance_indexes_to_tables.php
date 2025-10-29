<?php

declare(strict_types=1);

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
        Schema::table('pins', function (Blueprint $table) {
            // Add composite index for common queries
            $table->index(['result_id', 'use_status'], 'pins_result_status_index');
            // Add index for timestamp queries
            $table->index('created_at');
        });

        Schema::table('results', function (Blueprint $table) {
            // Add composite index for filtering and sorting
            $table->index(['course', 'grade'], 'results_course_grade_index');
            // Add index for timestamp queries
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pins', function (Blueprint $table) {
            $table->dropIndex('pins_result_status_index');
            $table->dropIndex(['created_at']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropIndex('results_course_grade_index');
            $table->dropIndex(['created_at']);
        });
    }
};
