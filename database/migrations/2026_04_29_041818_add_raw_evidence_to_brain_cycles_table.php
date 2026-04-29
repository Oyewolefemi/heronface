<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('brain_cycles', function (Blueprint $table) {
            $table->json('raw_evidence')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('brain_cycles', function (Blueprint $table) {
            $table->dropColumn('raw_evidence');
        });
    }
};