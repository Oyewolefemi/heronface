<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brain_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_number', 10);
            $table->text('directive_used')->nullable();
            $table->string('status', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brain_cycles');
    }
};