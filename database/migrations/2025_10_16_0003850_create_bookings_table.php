<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('service_option_id')->constrained('service_options')->cascadeOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 50);
            $table->dateTime('starts_at', 6); // UTC
            $table->dateTime('ends_at', 6);   // UTC
            $table->enum('status', ['booked', 'cancelled'])->default('booked');
            $table->timestamps(6);

            $table->index(['service_id', 'starts_at']);
            $table->index(['service_id', 'ends_at']);
            $table->index(['service_option_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};


