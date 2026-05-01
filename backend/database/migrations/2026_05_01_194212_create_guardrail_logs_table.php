<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardrail_logs', function (Blueprint $table) {
            $table->id();
            $table->json('original_payload')->nullable();
            $table->json('corrected_payload')->nullable();
            $table->string('decision');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardrail_logs');
    }
};
