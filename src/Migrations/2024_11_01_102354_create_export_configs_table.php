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
        Schema::create('export_configs', function (Blueprint $table) {
            $table->id();
            $table->string('module')->unique();
            $table->string('title');
            $table->string('query');
            $table->json('formatter')->nullable();
            $table->json('validator')->nullable();
            $table->json('default')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_configs');
    }
};
