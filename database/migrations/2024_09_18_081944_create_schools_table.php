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
            $table->unsignedBigInteger('type_education_id');
            $table->string('school_name');
            $table->string('logo_path')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('founded_year')->nullable();
            $table->string('registration_number')->nullable();
            $table->unsignedSmallInteger('status')->default(1);
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
