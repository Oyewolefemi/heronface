<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intelligence_pods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Asiko", "EcoVibes"
            $table->string('slug')->unique(); // e.g., "asiko", "ecovibes"
            $table->string('work_profile'); // "business_watch", "editorial", "infrastructure"
            $table->integer('search_limit')->default(5); // Throttle to prevent crashes
            $table->string('duty_cycle')->default('manual'); // "manual", "scheduled", "24/7"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('intelligence_pods');
    }
};