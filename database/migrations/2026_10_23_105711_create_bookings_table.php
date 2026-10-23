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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->string('reference')->unique();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->unsignedSmallInteger('guests');
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedInteger('total_minor');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->index(['room_id', 'status', 'check_in', 'check_out']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
