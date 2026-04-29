<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collision_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brain_cycle_id')->constrained()->cascadeOnDelete();
            $table->string('signal_context', 255);
            $table->string('platform_factor', 255);
            $table->text('inference');
            $table->string('severity', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collision_points');
    }
};