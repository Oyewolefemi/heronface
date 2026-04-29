<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ecosystem_nodes', function (Blueprint $table) {
            $table->json('schema_map')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('ecosystem_nodes', function (Blueprint $table) {
            $table->dropColumn('schema_map');
        });
    }
};