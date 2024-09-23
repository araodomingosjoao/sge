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
        Schema::create('photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('path')->default('local');
            $table->string('provider')->nullable();
            $table->uuid('imageable_id');
            $table->string('imageable_type');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['imageable_id', 'imageable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
