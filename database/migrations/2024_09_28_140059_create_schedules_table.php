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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('teacher_discipline_class_id')->constrained('teacher_discipline_class');
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('classroom')->nullable();
            $table->date('specific_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
