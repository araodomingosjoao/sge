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
        Schema::create('school_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trimester_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
            $table->softDeletes();
    
            $table->foreign('trimester_id')->references('id')->on('trimesters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};
