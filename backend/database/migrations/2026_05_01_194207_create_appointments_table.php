<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('customer_contact')->nullable();
            $table->string('service');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->unsignedInteger('people')->default(1);
            $table->string('status')->default('confirmed');
            $table->timestamps();

            $table->unique(['appointment_date', 'appointment_time', 'service'], 'appointments_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
