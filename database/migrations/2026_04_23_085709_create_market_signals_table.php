<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brain_cycle_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('source', 100)->nullable();
            $table->text('url')->nullable();
            $table->text('explained_content')->nullable();
            $table->string('color_tag', 20)->default('gray');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_signals');
    }
};