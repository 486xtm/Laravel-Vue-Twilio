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
        Schema::table('form_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->after('campaign_id')->default(1);
        });

        Schema::table('form_campaigns', function($table) {
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_campaigns', function (Blueprint $table) {
            Schema::dropIfExists('product_id');
        });
    }
};
