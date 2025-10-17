<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday'); // 1=Mon..7=Sun
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('slot_step_minutes')->default(30);
            $table->boolean('is_open')->default(true);
            $table->timestamps(6);

            $table->unique(['service_id', 'weekday']);
            $table->index(['service_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_schedules');
    }
};


