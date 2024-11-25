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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained('users', 'id')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('destination')->nullable();
            $table->string('distance')->nullable();
            $table->string('DriverId')->nullable();
            $table->string('paymentStatus')->nullable();
            $table->string('vehicleId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
