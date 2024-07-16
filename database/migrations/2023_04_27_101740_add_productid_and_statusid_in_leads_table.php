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
            $table->unsignedBigInteger('product_id')->after('celular')->nullable();
            $table->unsignedBigInteger('status_id')->after('mensagem');
        });

        Schema::table('leads', function($table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('status_id')->references('id')->on('status');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'status_id']);
        });
    }
};
