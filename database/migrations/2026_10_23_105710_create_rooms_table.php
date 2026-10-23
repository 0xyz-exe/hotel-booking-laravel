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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->unsignedSmallInteger('capacity');
            $table->unsignedInteger('nightly_rate_minor');
            $table->json('facilities')->nullable();
            $table->unsignedSmallInteger('inventory')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['hotel_id', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
