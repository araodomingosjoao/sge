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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools');
            $table->foreignUuid('class_id')->constrained('classes');
            $table->foreignUuid('discipline_id')->constrained('disciplines');
            $table->foreignUuid('trimester_id')->constrained('trimesters');
            $table->unsignedBigInteger('test_type_id')->constrained('test_types');
            $table->string('title');
            $table->date('evaluation_date');
            $table->decimal('max_grade', 5, 2);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
