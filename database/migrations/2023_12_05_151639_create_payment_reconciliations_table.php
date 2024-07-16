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
        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('quota');
            $table->timestamp('due_date');
            $table->timestamp('conciliation_date')->nullable();
            $table->decimal('total', 16, 2)->default(0);
            $table->unsignedBigInteger('receivable_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('status');
            $table->foreign('receivable_id')->references('id')->on('receivables');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliations');
    }
};
