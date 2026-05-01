<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_rules', function (Blueprint $table) {
            $table->id();
            $table->time('business_start_time')->default('08:00:00');
            $table->time('business_end_time')->default('18:00:00');
            $table->unsignedInteger('max_people')->default(2);
            $table->unsignedInteger('appointment_duration_minutes')->default(30);
            $table->boolean('allow_autocorrection')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_rules');
    }
};
