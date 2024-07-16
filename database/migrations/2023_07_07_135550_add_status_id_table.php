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
        Schema::table('origems', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->after('name')->default(1);
        });

        Schema::table('origems', function($table) {
            $table->foreign('status_id')->references('id')->on('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('origems', function (Blueprint $table) {
            Schema::dropIfExists('status_id');
        });
    }
};
