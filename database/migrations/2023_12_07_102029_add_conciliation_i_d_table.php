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
        Schema::table('receivables', function (Blueprint $table) {
            $table->unsignedBigInteger('conciliation_id')->after('proposal_id')->nullable();
        });

        Schema::table('receivables', function($table) {
            $table->foreign('conciliation_id')->references('id')->on('payment_reconciliations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            Schema::dropIfExists('conciliation_id');
        });
    }
};
