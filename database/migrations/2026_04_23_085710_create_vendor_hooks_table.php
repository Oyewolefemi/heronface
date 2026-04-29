<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_hooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brain_cycle_id')->constrained()->cascadeOnDelete();
            $table->string('vendor_name', 100);
            $table->text('hook_text');
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_hooks');
    }
};