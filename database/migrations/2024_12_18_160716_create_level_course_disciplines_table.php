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
        Schema::create('level_course_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('level_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('discipline_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['level_id', 'course_id', 'discipline_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_course_disciplines');
    }
};
