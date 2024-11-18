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
        Schema::create('grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->foreignUuid('class_id')->constrained('classes');
            // $table->foreignUuid('evaluation_id')->nullable()->constrained('evaluations');
            $table->uuid('discipline_id');
            $table->uuid('trimester_id');
            $table->unsignedBigInteger('test_type_id');
            $table->decimal('grade', 5, 2);
            $table->year('academic_year');
            $table->text('observation')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');
            $table->foreign('trimester_id')->references('id')->on('trimesters')->onDelete('cascade');
            $table->foreign('test_type_id')->references('id')->on('test_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
