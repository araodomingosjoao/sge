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
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->uuid('course_id')->nullable();
            $table->uuid('level_id');
            $table->string('name');
            $table->integer('maximum_students')->nullable();
            $table->year('academic_year');
            $table->enum('shift', ['morning', 'afternoon', 'evening'])->nullable();
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('level_id')->references('id')->on('levels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_models');
    }
};
