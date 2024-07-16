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
        Schema::table('user_queues', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->after('user_id');           
        });
        
        Schema::table('user_queues', function($table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_queues', function (Blueprint $table) {
            Schema::dropIfExists('company_id');
        });
    }
};
