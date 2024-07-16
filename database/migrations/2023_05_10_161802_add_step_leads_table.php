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
        Schema::table('leads', function (Blueprint $table) {            
            $table->unsignedBigInteger('funnel_step_id')->after('user_id')->default(1);
        });

        Schema::table('leads', function($table) {
            $table->foreign('funnel_step_id')->references('id')->on('funnel_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['funnel_step_id']);
        });
    }
};
