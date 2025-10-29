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
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->string('pin')->index();
            $table->string('serial_number')->index();
            $table->unsignedInteger('count')->default(0);
            $table->foreignId('result_id')->nullable()->constrained('results')->nullOnDelete();
            $table->string('use_status')->default('');
            $table->timestamps();

            $table->unique(['pin', 'serial_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};
