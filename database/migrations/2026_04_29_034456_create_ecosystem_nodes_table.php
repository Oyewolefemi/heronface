<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ecosystem_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Asiko Database" or "Omidan Core App"
            $table->string('type'); // "database" or "app"
            $table->string('host_url'); // The DB host IP or the App URL
            $table->string('db_driver')->nullable(); // "mysql", "pgsql", "sqlite"
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->text('db_password')->nullable(); // We will store this encrypted
            $table->string('status')->default('pending'); // "verified", "offline", "pending"
            $table->timestamp('last_ping')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ecosystem_nodes');
    }
};