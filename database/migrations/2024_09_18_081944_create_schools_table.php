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
        Schema::create('schools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_education_id');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('type_education_id')->references('id')->on('type_educations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
