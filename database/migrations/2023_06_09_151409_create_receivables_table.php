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
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('type');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descount', 12, 2);
            $table->decimal('interest', 12, 2);
            $table->decimal('total', 12, 2);
            $table->dateTime('due_date');
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            $table->foreign('proposal_id')->references('id')->on('proposals')
            ->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
