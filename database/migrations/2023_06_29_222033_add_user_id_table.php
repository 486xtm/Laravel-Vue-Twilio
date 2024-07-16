<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ready_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('message')->nullable();
        });

        Schema::table('ready_messages', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ready_messages', function (Blueprint $table) {
            Schema::dropIfExists('user_id');
        });
    }
};
