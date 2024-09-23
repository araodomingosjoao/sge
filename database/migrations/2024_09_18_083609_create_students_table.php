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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('class_id');
            $table->string('case_number')->nullable();
            $table->text('address')->nullable();
            $table->text('observation')->nullable();
            $table->date('birth_date');
            $table->unsignedSmallInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('class_id')->references('id')->on('classes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
